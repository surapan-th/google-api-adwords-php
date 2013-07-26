<?php
/**
 * This example show how to retrieve all keywords for a video campaign. To get
 * a list with all campaigns, run GetVideoCampaigns.php.
 *
 * Tags: VideoTargetingGroupCriterionService.get
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
$campaignId =  (integer) 'INSERT_CAMPAIGN_ID_HERE';

/**
 * Runs the example.
 * @param AdWordsUser $user the user to run the example with
 * @param string $campaignId the ID of the campaign to get keywords for
 */
function GetKeywordCriteriaExample(AdWordsUser $user, $campaignId) {
  $vtgcService = $user->GetService('VideoTargetingGroupCriterionService',
      ADWORDS_VERSION);

  // Get all the keywords for the campaign.
  $selector = new TargetingGroupCriterionSelector();
  $selector->campaignIds = array($campaignId);
  $selector->criterionTypes = 'SEARCH_KEYWORD';

  // Set selector paging (required by this service).
  $selector->paging = new Paging(0, AdWordsConstants::RECOMMENDED_PAGE_SIZE);

  $page = null;
  $negativeXsi = 'NegativeTargetingGroupCriterion';
  do {
    // Make the get request.
    $page = $vtgcService->get($selector);

    // Display results.
    if (isset($page->entries)) {
      foreach ($page->entries as $keyword) {
        printf(
          "Criterion%s ID %d, targeting group ID %d and text '%s'\n",
          ($keyword->getXsiTypeName() == $negativeXsi) ? ' (negative)' : '',
          $keyword->criterion->id,
          $keyword->targetingGroupId,
          $keyword->criterion->text
        );
      }
    } else {
      print "No keywords were found.\n";
    }

    // Advance the paging index.
    $selector->paging->startIndex += AdWordsConstants::RECOMMENDED_PAGE_SIZE;
  } while ($page->totalNumEntries > $selector->paging->startIndex);

  printf("\tTotal number of keywords found: %d", $page->totalNumEntries);
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
  GetKeywordsCriteriaExample($user, $campaignId);
} catch (OAuth2Exception $e) {
  ExampleUtils::CheckForOAuth2Errors($e);
} catch (ValidationException $e) {
  ExampleUtils::CheckForOAuth2Errors($e);
} catch (Exception $e) {
  printf("An error has occurred: %s\n", $e->getMessage());
}
