<?php

namespace Tests\Traits;

/**
 * Class ChecksURIsAuthorization
 */
trait ChecksURIsAuthorization
{
    /**
     * Test authorization and authentication for an URI.
     *
     * @param $uri
     * @param string $method
     */
    protected function check_authorization_uri($uri, $method = 'get') {
        $this->unauthorized_user_cannot_browse_uri($uri, $method);
        $this->an_user_cannot_browse_uri($uri, $method);
        $this->authorized_user_can_browse_uri($uri, $method);
    }

    /**
     * Test authorization and authentication for an URI api.
     *
     * @param $uri
     * @param string $method
     */
    protected function check_authorization_uri_api($uri, $method = 'get') {
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
        $response = $this->$method($uri);
        $response->assertStatus(302);
    }

    /**
     * Test and user cannot browser URI.
     *
     * @param $uri
     * @param string $method
     */
    protected function an_user_cannot_browse_uri($uri, $method = 'get')
    {
        $this->signIn();
        $response = $this->$method($uri);
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
        $this->signIn();
        $response = $this->$method($uri);
        $response->assertStatus(302);
    }

    /**
     * Test an authorized user can browse URI.
     *
     * @param $uri
     * @param string $method
     */
    protected function authorized_user_can_browse_uri( $uri, $method = 'get')
    {
        $this->signInAsRelationshipsManager()
            ->$method($uri)->assertStatus(200);
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