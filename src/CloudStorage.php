<?php

namespace GCPUtils;

use google\appengine\api\app_identity\AppIdentityService;

class CloudStorage
{
    const API_ACCESS_ENDPOINT = 'https://storage.googleapis.com';

    /**
     * Generate signed URL to provide query-string auth'n to a resource
     *
     * @param string $resource   A pointer to a specific resource
     * @param int    $expiration When the signed URL shoud expire
     * @param array  $options
     *
     * @return string
     */
    public function generateSignedUrl($resource, $expiration, array $options = [])
    {
        $options = array_merge([
            'method' => 'GET',
            'content_md5' => '',
            'content_type' => '',
        ], $options);

        $stringToSign = implode("\n", [
            $options['method'],
            $options['content_md5'],
            $options['content_type'],
            $expiration,
            $resource,
        ]);

        $queryParams = $this->getSignedQueryParams($expiration, $stringToSign);

        if (isset($options['response_type'])) {
            $queryParams['response-content-type'] = $options['response_type'];
        }

        if (isset($options['response_disposition'])) {
            $queryParams['response-content-disposition'] = $options['response_disposition'];
        }

        if (isset($options['generation'])) {
            $queryParams['generation'] = $options['generation'];
        }

        return sprintf('%s%s?%s', static::API_ACCESS_ENDPOINT,
            $resource, http_build_query($queryParams));
    }

    private function getSignedQueryParams($expiration, $stringToSign)
    {
        $sign = AppIdentityService::signForApp($stringToSign);

        $signature = base64_encode($sign['signature']);

        return [
            'GoogleAccessId' => AppIdentityService::getServiceAccountName(),
            'Expires' => $expiration,
            'Signature' => $signature,
        ];
    }
}
