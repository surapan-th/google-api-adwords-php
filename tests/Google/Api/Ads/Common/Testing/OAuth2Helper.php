<?php
/**
 * Copyright 2013, Google Inc. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the 'License');
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an 'AS IS' BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @package    GoogleApiAdsCommon
 * @subpackage Testing
 * @category   WebServices
 * @copyright  2013, Google Inc. All Rights Reserved.
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License,
 *             Version 2.0
 * @author     Paul Matthews
 */

require_once 'Google/Api/Ads/Common/Util/XmlUtils.php';

/**
 * A Helper class to facilitate OAuth2 Testing.
 *
 * @package GoogleApiAdsAdWords
 * @subpackage Testing
 */
class OAuth2Helper {

  /**
   * Asset directory
   *
   * @var string
   **/
  private $assetDirectory;

  /**
   * Default extension for this asset helper
   *
   * @var string
   **/
  private $defaultExt;

  /**
   * Constructor.
   *
   * @param string $assetDirectory the path to the directory containing assets.
   * @param null|string $defaultExt the default extension for the assets.
   */
  public function __construct() {
  }

  public function GetValidCredenitals() {
    return $this->GetValidTemplate();
  }

  public function SetLongExpiredCredentials($credentials = NULL) {
    $credentials = !is_null($credentials) ?
        $credentials : $this->GetValidTemplate();
    $expiredOverrides = array(
      'timestamp' => strtotime('-1 day');
    );
    return $this->OverrideCredentials($credentials, $expiredOverrides);
  }

  public function SetInExpiredBufferCredentials($credentials = NULL) {
    $credentials = !is_null($credentials) ?
        $credentials : $this->GetValidTemplate();
    $refreshBuffer = TestOAuth2Handler::REFRESH_BUFFER;
    $secondsInterval = (int) $credentials['expires'] - ($refreshBuffer / 2);
    $expiredOverrides = array(
      'timestamp' => strtotime(sprintf('-%d seconds', $secondsInterval))
    );
    return $this->OverrideCredentials($credentials, $expiredOverrides);
  }

  public function RemoveAccessTokenCredentials($credentials = NULL) {
    $credentials = !is_null($credentials) ?
        $credentials : $this->GetValidTemplate();

    unset($credentials['access_token']);
    unset($credentials['expires_in']);
    unset($credentials['timestamp']);

    return $credentials;
  }

  public function RemoveRefreshTokenCredentials($credentials = NULL) {
    $credentials = !is_null($credentials) ?
        $credentials : $this->GetValidTemplate();
    unset($credentials['refresh_token']);
    return $credentials;
  }

  public function OverrideCredentials($source = NULL, $overrides = NULL) {
    $source = empty($source) ? array() : $source;
    $overrides = empty($overrides) ? array() : $overrides;

    return array_merge($source, $overrides);
  }

  protected function GetValidTemplate() {
    return array(
      'access_token' => sprintf('TEST_ACCESS_TOKEN_%s', uniqid()),
      'refresh_token' => sprintf('TEST_REFRESH_TOKEN_%s', uniqid()),
      'expires_in' => '3600',
      'timestamp' => time(),
      'client_id' => sprintf('TEST_CLIENT_ID_%s', uniqid()),
      'client_secret' => sprintf('TEST_CLIENT_SECRET_%s', uniqid),
    );
  }
}
