<?php
/**
 * This example illustrates how to create a video call to action overlay.
 *
 * Tags: VideoService.mutateCallToAction
 *
 * Copyright 2013, Google Inc. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @package    GoogleApiAdsAdWords
 * @subpackage v201302
 * @category   WebServices
 * @copyright  2013, Google Inc. All Rights Reserved.
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License,
 *             Version 2.0
 * @author     Paul Matthews
 */

// Include the initialization file
require_once dirname(dirname(__FILE__)) . '/init.php';

// Enter parameters required by the code example.
$videoId = 'INSERT_VIDEO_ID_HERE';

/**
 * Runs the example.
 * @param AdWordsUser $user the user to run the example with
 * @param string $videoId the video ID to add a call to action to.
 */
function AddVideoCallToActionExample(AdWordsUser $user, $videoId) {
  $videoService = $user->GetService('VideoService', ADWORDS_VERSION);

  $videoCallToAction = new VideoCallToAction();
  $videoCallToAction->id = $videoId;
  $callToAction = new CallToAction();
  $creative = new CallToActionCreative();
  $creative->headline = 'Mars cruises';
  $creative->descriptionLine1 = 'Astonishing views';
  $creative->descriptionLine2 = 'Mild climate';
  $creative->displayUrl = 'wwww.example.com/mars';
  $creative->destinationUrl = 'wwww.example.com/mars';
  $callToAction->creative = $creative;
  $videoCallToAction->callToAction = $callToAction;

  $videoCallToActionOperation = new VideoCallToActionOperation();
  $videoCallToActionOperation->operator = 'SET';
  $videoCallToActionOperation->operand = $videoCallToAction;

  $response = $videoService->mutateCallToAction(
      array($videoCallToActionOperation));

  if (!empty($response->value)) {
    foreach ($response->value as $videoCallToAction) {
      printf(
        "CallToAction overlay was added to video ID '%s', headline '%s'.\n",
        $videoCallToAction->videoId,
        $videoCallToAction->callToAction->creative->headline
      );
    }
  } else {
    echo "No call to action overlays were added.\n";
  }
}

// Don't run the example if the file is being included.
if (__FILE__ != realpath($_SERVER['PHP_SELF'])) {
  return;
}

try {
  // Get AdWordsUser from credentials in "../auth.ini"
  // relative to the AdWordsUser.php file's directory.
  $user = new AdWordsUser();

  // Log every SOAP XML request and response.
  $user->LogAll();

  // Run the example.
  AddVideoCallToActionExample($user, $videoId);
} catch (OAuth2Exception $e) {
  ExampleUtils::CheckForOAuth2Errors($e);
} catch (ValidationException $e) {
  ExampleUtils::CheckForOAuth2Errors($e);
} catch (Exception $e) {
  printf("An error has occurred: %s\n", $e->getMessage());
}
