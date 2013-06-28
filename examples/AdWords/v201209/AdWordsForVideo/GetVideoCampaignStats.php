<?php
/**
 * This example illustrates how to retrieve stats for a video campaign.
 *
 * Tags: VideoCampaignService.get
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
 * @subpackage v201209
 * @category   WebServices
 * @copyright  2013, Google Inc. All Rights Reserved.
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License,
 *             Version 2.0
 * @author     Paul Matthews
 */

// Include the initialization file
require_once dirname(dirname(__FILE__)) . '/init.php';

// Enter parameters required by the code example.
$campaignId = (integer) 'INSERT_CAMPAIGN_ID_HERE';

/**
 * Runs the example.
 * @param AdWordsUser $user the user to run the example with
 * @param string $campaignId the ID of the campaign to get stats for
 */
function GetVideoCampaignStatsExample(AdWordsUser $user, $campaignId) {
  $vcService =$user->GetService('VideoCampaignService',
      ADWORDS_VERSION);

  // Preparing dates to get stats for this year.
  $dateRange = new DateRange();
  $dateRange->min = date('Ymd', strtotime('first day of january'));
  $dateRange->max = date('Ymd');

  // Get stats for the campaign for given dates.
  $selector = new VideoCampaignSelector();
  $selector->ids = array($campaignId);
  $selector->statsSelector = new StatsSelector();
  $selector->statsSelector->dateRange = $dateRange;
  $selector->statsSelector->segmentationDimensions = array('DATE_MONTH');
  $selector->statsSelector->metrics = array('VIEWS', 'COST', 'AVERAGE_CPV');
  $selector->statsSelector->summaryTypes = array('ALL');
  $selector->statsSelector->segmentedSummaryType = 'ALL';

  // Set selector paging (required by this service).
  $selector->paging = new Paging(0, AdWordsConstants::RECOMMENDED_PAGE_SIZE);

  $page = $vcService->get($selector);

  if (isset($page->entries)) {
    $campaign = array_shift($page->entries);
    printf("Campaign ID %d, name '%s' and status '%s'", $campaign->id,
        $campaign->name, $campaign->status);

    if (!empty($campaign->stats)) {
      sprintf("\tCampaign Stats:\n\t\t%s", FormatStats($campaign->stats));
    }

    if (!empty($campaign->segmentedStats)) {
      foreach ($campaign->segmentedStats as $stats) {
        $segmentKeyStr = $stats->segmentKey->DateKey->date;
        sprintf("\tCampaign Segmented Stats for month of: %s\n\t\t%s",
          $segmentKeyStr, FormatStats($stats));
      }
    }
    if (!empty($page->summaryStats)) {
      foreach ($page->summaryStats as $stats) {
        printf("\tSummary of type %s\n\t\t%s", $stats->summaryType,
              FormatStats($stats));
      }
    }
  } else {
    print "No video campaigns were found.\n";
  }
}

function FormatStats($stats) {
  return sprintf(
    "Views: %s, Cost: %s, Avg. CPC: %s, Avg. CPV: %s, " .
        "Avg. CPM: %s, 25%%: %s, 50%%: %s, 75%%: %s, 100%%: %s",
    (empty($stats->views)) ? '--' : $stats->views,
    (empty($stats->cost)) ? '--' : $stats->cost->microAmount,
    (empty($stats->averageCpc)) ? '--' : $stats->averageCpc->microAmount,
    (empty($stats->averageCpv)) ? '--' : $stats->averageCpv->microAmount,
    (empty($stats->averageCpm)) ? '--' : $stats->averageCpm->microAmount,
    (empty($stats->quartile25Percents)) ? '--' : $stats->quartile25Percents,
    (empty($stats->quartile50Percents)) ? '--' : $stats->quartile50Percents,
    (empty($stats->quartile75Percents)) ? '--' : $stats->quartile75Percents,
    (empty($stats->quartile100Percents)) ? '--' : $stats->quartile100Percents
  );
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
  GetCampaignStatsExample($user, $campaignId);
} catch (OAuth2Exception $e) {
  ExampleUtils::CheckForOAuth2Errors($e);
} catch (ValidationException $e) {
  ExampleUtils::CheckForOAuth2Errors($e);
} catch (Exception $e) {
  printf("An error has occurred: %s\n", $e->getMessage());
}

