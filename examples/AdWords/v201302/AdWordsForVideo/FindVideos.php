<?php
/**
 * This example illustrates how to find YouTube videos by a search string. It
 * retrieves details for the first 100 matching videos.
 *
 * Tags: VideoService.search
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
$queryString = (string) 'INSERT_QUERY_STRING_HERE';

/**
 * Runs the example.
 * @param AdWordsUser $user the user to run the example with
 * @param string $queryString the query string to use
 */
function FindVideosExample(AdWordsUser $user, $queryString) {
  $videoService = $user->GetService('VideoService', ADWORDS_VERSION);
  $pageSize = 100;

  // Create a selector.
  $selector = new VideoSearchSelector();
  $selector->searchType = 'VIDEO';
  $selector->query = $queryString;

  // Set selector paging (required by this service).
  $selector->paging = new Paging(0, $pageSize);

  $total = 0;
  // Make the get request.
  $page = $videoService->search($selector);

  // Display results.
  if (isset($page->entries)) {
    foreach ($page->entries as $video) {
      printf("YouTube video ID '%s' with title '%s' found.\n", $video->id,
          $video->title);
    }

    printf("\tTotal number of matching videos: %d", $page->totalNumEntries);
  } else {
    print "No YouTube videos were found.\n";
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
  FindVideosExample($user, $queryString);
} catch (OAuth2Exception $e) {
  ExampleUtils::CheckForOAuth2Errors($e);
} catch (ValidationException $e) {
  ExampleUtils::CheckForOAuth2Errors($e);
} catch (Exception $e) {
  printf("An error has occurred: %s\n", $e->getMessage());
}
