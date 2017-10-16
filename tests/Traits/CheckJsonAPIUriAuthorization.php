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
    protected function check_json_api_uri_authorization($uri, $method = 'get', $attributes = []) {
        $this->unauthorized_user_cannot_browse_uri($uri, $method, $attributes);
        $this->an_user_cannot_browse_uri_api($uri, $method, $attributes);
        $this->authorized_user_can_browse_uri_api($uri, $method, $attributes);
    }

    /**
     * Test unauthorized user cannot browse URL.
     *
     * @param $uri
     * @param string $method
     */
    protected function unauthorized_user_cannot_browse_uri($uri, $method = 'get', $attributes = [])
    {
        $response = $this->json(strtoupper($method),$uri, $attributes);
        $response->assertStatus(401);
    }

    /**
     * Test and user cannot browser URI.
     *
     * @param $uri
     * @param string $method
     * @param array $attributes
     * @param null $user
     */
    protected function an_user_cannot_browse_uri($uri, $method = 'get', $attributes = [],$user = null)
    {
        $this->signIn($user,'api');
        $response = $this->json(strtoupper($method),$uri, $attributes);
        $response->assertStatus(403);
    }

    /**
     * Test and user cannot browser URI.
     *
     * @param $uri
     * @param string $method
     * @param array $attributes
     * @param null $user
     */
    protected function an_user_cannot_browse_uri_api($uri, $method = 'get', $attributes = [],$user = null)
    {
        $this->signIn($user,'api');
        $response = $this->json(strtoupper($method),$uri, $attributes);
        $response->assertStatus(403);
    }

    /**
     * Test an authorized user can browse URI.
     *
     * @param $uri
     * @param string $method
     */
    protected function authorized_user_can_browse_uri_api( $uri, $method = 'get', $attributes = [])
    {
//        dump($method);
//        dd($uri);
        $this->signInAsRelationshipsManager('api')
            ->json(strtoupper($method),$uri, $attributes)->assertStatus(200);
    }

}