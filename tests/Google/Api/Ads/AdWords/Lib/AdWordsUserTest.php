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
 * @subpackage Util
 * @category   WebServices
 * @copyright  2013, Google Inc. All Rights Reserved.
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License,
 *             Version 2.0
 * @author     Paul Matthews
 * @author     Vincent Tsao
 */
error_reporting(E_STRICT | E_ALL);

require_once 'Google/Api/Ads/AdWords/Lib/AdWordsUser.php';

/**
 * Unit tests for {@link AdWordsUser}.
 * @group small
 */
class AdWordsUserTest extends PHPUnit_Framework_TestCase {

  /**
   * Tests that the user agent header is properly set for this client library.
   *
   * @covers AdsUser::GetClientLibraryUserAgent
   */
  public function testGetClientLibraryUserAgent() {
    $USER_AGENT = 'AdWordsApiPhpClient-test';
    $LIB_NAME = 'AwApi-PHP';
    $COMMON_NAME = 'Common-PHP';
    $VERSION_REGEX = '\d{1,2}\.\d{1,2}\.\d{1,2}';

    $authIniFilePath = tempnam(sys_get_temp_dir(), 'auth.ini.');
    $settingsIniFilePath = tempnam(sys_get_temp_dir(), 'settings.ini.');
    $user = new AdWordsUser($authIniFilePath, NULL, NULL, NULL, NULL,
        $USER_AGENT, NULL, $settingsIniFilePath);

    // Example: "testApplication (AwApi-PHP/4.1.1, Common-PHP/5.0.0, PHP/5.4.8)"
    $search = sprintf(
      '/^%s \(%s\/%s, %s\/%s, PHP\/%s\)$/',
      preg_quote($user->GetUserAgent()),
      preg_quote($LIB_NAME),
      $VERSION_REGEX,
      preg_quote($COMMON_NAME),
      $VERSION_REGEX,
      preg_quote(PHP_VERSION)
    );

    $this->assertRegExp($search, $user->GetClientLibraryUserAgent());
  }
}

