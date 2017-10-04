<?php

namespace Tests\Traits;

/**
 * Class CheckJsonAPIUriAuthorization
 */
trait CheckJsonAPIUriAuthorization
{
    /**
     * Test authorization and authentication for an URI api.
     *
     * @param $uri
     * @param string $method
     */
    protected function check_json_api_uri_authorization($uri, $method = 'get') {
        $this->unauthorized_user_cannot_browse_uri($uri, $method);
        $this->an_user_cannot_browse_uri_api($uri, $method);
        $this->authorized_user_can_browse_uri_api($uri, $method);
    }

    /**
     * Test unauthorized user cannot browse URL.
     *
     * @param $uri
     * @param string $method
     */
    protected function unauthorized_user_cannot_browse_uri($uri, $method = 'get')
    {
        $response = $this->json(strtoupper($method),$uri);
        $response->assertStatus(401);
    }

    /**
     * Test and user cannot browser URI.
     *
     * @param $uri
     * @param string $method
     */
    protected function an_user_cannot_browse_uri($uri, $method = 'get')
    {
        $this->signIn(null,'api');
        $response = $this->json(strtoupper($method),$uri);
        $response->assertStatus(403);
    }

    /**
     * Test and user cannot browser URI.
     *
     * @param $uri
     * @param string $method
     */
    protected function an_user_cannot_browse_uri_api($uri, $method = 'get')
    {
        $this->signIn(null,'api');
        $response = $this->json(strtoupper($method),$uri);
        $response->assertStatus(403);
    }

    /**
     * Test an authorized user can browse URI.
     *
     * @param $uri
     * @param string $method
     */
    protected function authorized_user_can_browse_uri_api( $uri, $method = 'get')
    {
        $this->signInAsRelationshipsManager('api')
            ->$method($uri)->assertStatus(200);
    }

}