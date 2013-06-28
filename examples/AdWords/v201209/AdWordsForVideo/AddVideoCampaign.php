<?php
/**
 * This example illustrates how to create a video campaign.
 *
 * Tags: VideoCampaignService.mutate, BudgetService.mutate
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

/**
 * Runs the example.
 * @param AdWordsUser $user the user to run the example with
 */
function AddVideoCampaignExample(AdWordsUser $user) {
  $budgetService = $user->GetService('BudgetService', ADWORDS_VERSION);
  $videoCampaignService = $user->GetService('VideoCampaignService',
      ADWORDS_VERSION);

  // Create a budget, which can be shared by multiple campaigns.
  $budget = new Budget();
  $budget->name = sprintf("Video Budget #%s", uniqid());
  $budget->amount = new Money(50000000);
  $budget->deliveryMethod = 'STANDARD';
  $budget->period = 'DAILY';

  $budgetOperation = new BudgetOperation();
  $budgetOperation->operator = 'ADD';
  $budgetOperation->operand = $budget;

  // Add budget.
  $returnBudget = $budgetService->mutate($budgetOperation);
  $budgetId = $returnBudget->value[0]->budgetId;

  // Create video campaign.
  $campaign = new VideoCampaign();
  $campaign->name = sprintf("Interplanetary Video #%s", uniqid());
  $campaign->status = 'PAUSED';
  $campaign->budgetId = $budgetId;

  // Optional Fields:
  $campaign->startDate = date('Ymd', strtotime('+1 day'));

  // Prepare for adding campaign.
  $campaignOperation = new VideoCampaignOperation();
  $campaignOperation->operator = 'ADD';
  $campaignOperation->operand = $campaign;

  // Add video campaign.
  $response = $videoCampaignService->mutate(array($campaignOperation));

  if (!empty($response->value)) {
    foreach ($response->value as $campaign) {
      printf("Campaign with name '%s' and id '%s' was added.\n",
          $campaign->name, $campaign->id);
    }
  } else {
    echo "No campaigns were added.\n";
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
  AddVideoCampaignExample($user);
} catch (OAuth2Exception $e) {
  ExampleUtils::CheckForOAuth2Errors($e);
} catch (ValidationException $e) {
  ExampleUtils::CheckForOAuth2Errors($e);
} catch (Exception $e) {
  printf("An error has occurred: %s\n", $e->getMessage());
}
