<?php

namespace GoogleOneTap\Services;

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use phpseclib3\Crypt\AES;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Math\BigInteger;

class GoogleOneTapVerifyJwt
{
    const FEDERATED_SIGNON_CERT_URL = 'https://www.googleapis.com/oauth2/v3/certs';
    const OAUTH2_ISSUER = 'accounts.google.com';
    const OAUTH2_ISSUER_HTTPS = 'https://accounts.google.com';

    public function __construct(private $jwt = null)
    {
        $this->jwt = $jwt ?: $this->getJwtService();
    }


    /**
     * Verifies an id token and returns the authenticated apiLoginTicket.
     * Throws an exception if the id token is not valid.
     * The audience parameter can be used to control which id tokens are
     * accepted.  By default, the id token must have been issued to this OAuth2 client.
     *
     * @param string  $idToken  the ID token in JWT format
     * @param ?string $audience Optional. The audience to verify against JWt "aud"
     *
     * @return array|false the token payload, if successful
     */
    public function verifyIdToken(string $idToken, string $audience = null): array|false
    {
        if (empty($idToken)) return false;

        // set phpseclib constants if applicable
        $this->setPhpsecConstants();

        // Check signature
        $certs = $this->getFederatedSignOnCerts();
        foreach ($certs as $cert) {
            try {
                $args = [$idToken];
                $publicKey = $this->getPublicKey($cert);
                if (class_exists(Key::class)) {
                    $args[] = new Key($publicKey, 'RS256');
                } else {
                    $args[] = $publicKey;
                    $args[] = ['RS256'];
                }
                $payload = \call_user_func_array([$this->jwt, 'decode'], $args);

                if (property_exists($payload, 'aud')) {
                    if ($audience && $payload->aud != $audience) return false;
                }

                // support HTTP and HTTPS issuers
                // @see https://developers.google.com/identity/sign-in/web/backend-auth
                $issuers = [self::OAUTH2_ISSUER, self::OAUTH2_ISSUER_HTTPS];
                if (!isset($payload->iss) || !in_array($payload->iss, $issuers)) return false;

                return (array)$payload;
            } catch (ExpiredException $e) { // @phpstan-ignore-line
                return false;
            } catch (SignatureInvalidException $e) {
                // continue
            }
        }

        return false;
    }

    private function getPublicKey($cert)
    {
        $modulus = new BigInteger($this->jwt->urlsafeB64Decode($cert['n']), 256);
        $exponent = new BigInteger($this->jwt->urlsafeB64Decode($cert['e']), 256);
        $component = ['n' => $modulus, 'e' => $exponent];

        $loader = PublicKeyLoader::load($component);

        return $loader->toString('PKCS8');
    }

    private function getJwtService(): JWT
    {
        $jwt = new JWT();
        if ($jwt::$leeway < 1) {
            // Ensures JWT leeway is at least 1
            // @see https://github.com/google/google-api-php-client/issues/827
            $jwt::$leeway = 1;
        }

        return $jwt;
    }

    /**
     * Retrieve and cache a certificates file.
     *
     * @param string $url location
     *
     * @return array certificates
     * @throws \Exception
     */
    private function retrieveCertsFromLocation(string $url): array
    {
        // If we're retrieving a local file, just grab it.
        if (!str_starts_with($url, 'http')) {
            if (!$file = file_get_contents($url)) throw new \Exception(
                "Failed to retrieve verification certificates: '" .
                $url . "'."
            );

            return json_decode($file, true);
        }

        $res = Http::get($url);

        if ($res->ok()) return $res->json();
        throw new \Exception(
            sprintf(
                'Failed to retrieve verification certificates: "%s".',
                $res->body()
            ),
            $res->status()
        );
    }


    // Gets federated sign-on certificates to use for verifying identity tokens.
    // Returns certs as array structure, where keys are key ids, and values
    // are PEM encoded certificates.
    private function getFederatedSignOnCerts()
    {
        $certs = Cache::remember('federated_signon_certs_v3', now()->addDay(), function () {
            return $this->retrieveCertsFromLocation(self::FEDERATED_SIGNON_CERT_URL);
        });

        if (!isset($certs['keys'])) {
            throw new InvalidArgumentException(
                'federated sign-on certs expects "keys" to be set'
            );
        }

        return $certs['keys'];
    }

    /**
     * phpseclib calls "phpinfo" by default, which requires special
     * whitelisting in the AppEngine VM environment. This function
     * sets constants to bypass the need for phpseclib to check phpinfo
     *
     * @see phpseclib/Math/BigInteger
     * @see https://github.com/GoogleCloudPlatform/getting-started-php/issues/85
     */
    private function setPhpsecConstants(): void
    {
        if (filter_var(getenv('GAE_VM'), FILTER_VALIDATE_BOOLEAN)) {
            if (!defined('MATH_BIGINTEGER_OPENSSL_ENABLED')) {
                define('MATH_BIGINTEGER_OPENSSL_ENABLED', true);
            }
            if (!defined('CRYPT_RSA_MODE')) {
                define('CRYPT_RSA_MODE', AES::ENGINE_OPENSSL);
            }
        }
    }
}
