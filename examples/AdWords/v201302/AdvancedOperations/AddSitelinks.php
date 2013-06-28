<?php
/**
 * This example adds a sitelinks feed and associates it with a campaign.
 * To get campaigns, run GetCampaigns.php.
 *
 * Tags: CampaignFeedService.mutate, FeedItemService.mutate
 * Tags: FeedMappingService.mutate, FeedService.mutate
 * Restriction: adwords-only
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
 * @author     David Torres <api.davidtorres@gmail.com>
 */

// Include the initialization file
require_once dirname(dirname(__FILE__)) . '/init.php';

// Enter parameters required by the code example.
$campaignId = 'INSERT_CAMPAIGN_ID_HERE';

/**
 * Runs the example.
 * @param AdWordsUser $user the user to run the example with
 * @param string $campaignId the ID of the campaign to add the sitelinks to
 */
function AddSitelinksExample(AdWordsUser $user, $campaignId) {
  $sitelinksData = CreateSiteLinksFeed($user);
  $sitelinksData = CreateSiteLinksFeedItems($user, $sitelinksData);
  CreateSiteLinksFeedMapping($user, $sitelinksData);
  CreateSiteLinksCampaignFeed($user, $sitelinksData, $campaignId);
}

/**
 * Creates the feed that holds the sitelinks data.
 * @param AdWordsUser $user the user to run the example with
 */
function CreateSiteLinksFeed(AdWordsUser $user) {
  // Map that holds IDs associated to the feeds metadata.
  $sitelinksData = array();

  // Get the FeedService, which loads the required classes.
  $feedService = $user->GetService('FeedService', ADWORDS_VERSION);

  // Create attributes.
  $textAttribute = new FeedAttribute();
  $textAttribute->type = 'STRING';
  $textAttribute->name = 'Link Text';
  $urlAttribute = new FeedAttribute();
  $urlAttribute->type = 'URL';
  $urlAttribute->name = 'Link URL';

  // Create the feed.
  $sitelinksFeed = new Feed();
  $sitelinksFeed->name = 'Feed For Site Links';
  $sitelinksFeed->attributes = array($textAttribute, $urlAttribute);
  $sitelinksFeed->origin = 'USER';

  // Create operation.
  $operation = new FeedOperation();
  $operation->operator = 'ADD';
  $operation->operand = $sitelinksFeed;

  $operations = array($operation);

  // Add the feed.
  $result = $feedService->mutate($operations);

  $savedFeed = $result->value[0];
  $sitelinksData['sitelinksFeedId'] = $savedFeed->id;
  $savedAttributes = $savedFeed->attributes;
  $sitelinksData['linkTextFeedAttributeId'] = $savedAttributes[0]->id;
  $sitelinksData['linkUrlFeedAttributeId'] = $savedAttributes[1]->id;

  printf('Feed with name "%s" and ID %d with linkTextAttributeId %d'
      . " and linkUrlAttributeId %d was created.\n",
      $savedFeed->name,
      $savedFeed->id,
      $savedAttributes[0]->id,
      $savedAttributes[1]->id);

  return $sitelinksData;
}

/**
 * Adds sitelinks items to the feed.
 * @param AdWordsUser $user the user to run the example with
 * @param map $sitelinksData IDs associated to created sitelinks feed metadata
 */
function CreateSiteLinksFeedItems(AdWordsUser $user, $sitelinksData) {
  // Get the FeedItemService, which loads the required classes.
  $feedItemService = $user->GetService('FeedItemService', ADWORDS_VERSION);

  // Create operations to add FeedItems.
  $home = NewSiteLinkFeedItemAddOperation($sitelinksData, 'Home',
      'http://www.example.com');
  $stores = NewSiteLinkFeedItemAddOperation($sitelinksData, 'Stores',
      'http://www.example.com/stores');
  $onSale = NewSiteLinkFeedItemAddOperation($sitelinksData, 'On Sale',
      'http://www.example.com/sale');
  $support = NewSiteLinkFeedItemAddOperation($sitelinksData, 'Support',
      'http://www.example.com/support');
  $products = NewSiteLinkFeedItemAddOperation($sitelinksData, 'Products',
      'http://www.example.com/products');
  $aboutUs = NewSiteLinkFeedItemAddOperation($sitelinksData, 'About Us',
      'http://www.example.com/about');

  $operations = array($home, $stores, $onSale, $support, $products, $aboutUs);

  $result = $feedItemService->mutate($operations);
  $sitelinksData['siteLinkFeedItemIds'] = array();

  foreach ($result->value as $feedItem) {
    printf("FeedItem with feedItemId %d was added.\n", $feedItem->feedItemId);
    $sitelinksData['siteLinkFeedItemIds'][] = $feedItem->feedItemId;
  }

  return $sitelinksData;
}

// See the Placeholder reference page for a list of all the placeholder types and fields.
// https://developers.google.com/adwords/api/docs/appendix/placeholders.html
define('PLACEHOLDER_SITELINKS', 1);
define('PLACEHOLDER_FIELD_SITELINK_LINK_TEXT', 1);
define('PLACEHOLDER_FIELD_SITELINK_URL', 2);

/**
 * Maps the feed attributes to the sitelink placeholders.
 * @param AdWordsUser $user the user to run the example with
 * @param map $sitelinksData IDs associated to created sitelinks feed metadata
 */
function CreateSiteLinksFeedMapping(AdWordsUser $user, $sitelinksData) {
  // Get the FeedMappingService, which loads the required classes.
  $feedMappingService = $user->GetService('FeedMappingService',
      ADWORDS_VERSION);

  // Map the FeedAttributeIds to the fieldId constants.
  $linkTextFieldMapping = new AttributeFieldMapping();
  $linkTextFieldMapping->feedAttributeId =
      $sitelinksData['linkTextFeedAttributeId'];
  $linkTextFieldMapping->fieldId = PLACEHOLDER_FIELD_SITELINK_LINK_TEXT;
  $linkUrlFieldMapping = new AttributeFieldMapping();
  $linkUrlFieldMapping->feedAttributeId =
      $sitelinksData['linkUrlFeedAttributeId'];
  $linkUrlFieldMapping->fieldId = PLACEHOLDER_FIELD_SITELINK_URL;

  // Create the FieldMapping and operation.
  $feedMapping = new FeedMapping();
  $feedMapping->placeholderType = PLACEHOLDER_SITELINKS;
  $feedMapping->feedId = $sitelinksData['sitelinksFeedId'];
  $feedMapping->attributeFieldMappings =
      array($linkTextFieldMapping, $linkUrlFieldMapping);
  $operation = new FeedMappingOperation();
  $operation->operand = $feedMapping;
  $operation->operator = 'ADD';

  $operations = array($operation);

  // Save the field mapping.
  $result = $feedMappingService->mutate($operations);
  foreach ($result->value as $feedMapping) {
    printf('Feed mapping with ID %d and placeholderType %d was saved for ' .
        "feed with ID %d.\n",
        $feedMapping->feedMappingId,
        $feedMapping->placeholderType,
        $feedMapping->feedId);
  }
}

/**
 * Creates the CampaignFeed associated to the feed data already populated.
 * @param AdWordsUser $user the user to run the example with
 * @param map $sitelinksData IDs associated to created sitelinks feed metadata
 * @param string $campaignId the ID of the campaign to add the sitelinks to
 */
function CreateSiteLinksCampaignFeed(AdWordsUser $user, $sitelinksData,
    $campaignId) {
  // Get the CampaignFeedService, which loads the required classes.
  $campaignFeedService = $user->GetService('CampaignFeedService',
      ADWORDS_VERSION);

  $requestContextOperand = new RequestContextOperand();
  $requestContextOperand->contextType = 'FEED_ITEM_ID';

  $function = new FeedFunction();
  $function->lhsOperand = array($requestContextOperand);
  $function->operator = 'IN';

  $operands = array();
  foreach ($sitelinksData['siteLinkFeedItemIds'] as $feedItemId) {
    $constantOperand = new ConstantOperand();
    $constantOperand->longValue = $feedItemId;
    $constantOperand->type = 'LONG';
    $operands[] = $constantOperand;
  }
  $function->rhsOperand = $operands;

  $campaignFeed = new CampaignFeed();
  $campaignFeed->feedId = $sitelinksData['sitelinksFeedId'];
  $campaignFeed->campaignId = $campaignId;
  $campaignFeed->matchingFunction = $function;
  // Specifying placeholder types on the CampaignFeed allows the same feed
  // to be used for different placeholders in different Campaigns.
  $campaignFeed->placeholderTypes = array(PLACEHOLDER_SITELINKS);

  $operation = new CampaignFeedOperation();
  $operation->operand = $campaignFeed;
  $operation->operator = 'ADD';

  $operations = array($operation);

  $result = $campaignFeedService->mutate($operations);
  foreach ($result->value as $savedCampaignFeed) {
    printf("Campaign with ID %d was associated with feed with ID %d.\n",
        $savedCampaignFeed->campaignId,
        $savedCampaignFeed->feedId);
  }
}

/**
 * Creates a SitelinkFeedItem and wraps it in an ADD operation.
 * @param map $sitelinksData IDs associated to created sitelinks feed metadata
 * @param string $text text of the sitelink
 * @param string $url URL of the sitelink
 */
function NewSiteLinkFeedItemAddOperation($sitelinksData, $text, $url) {
  // Create the FeedItemAttributeValues for our text values.
  $linkTextAttributeValue = new FeedItemAttributeValue();
  $linkTextAttributeValue->feedAttributeId =
      $sitelinksData['linkTextFeedAttributeId'];
  $linkTextAttributeValue->stringValue = $text;
  $linkUrlAttributeValue = new FeedItemAttributeValue();
  $linkUrlAttributeValue->feedAttributeId =
      $sitelinksData['linkUrlFeedAttributeId'];
  $linkUrlAttributeValue->stringValue = $url;

  // Create the feed item and operation.
  $item = new FeedItem();
  $item->feedId = $sitelinksData['sitelinksFeedId'];
  $item->attributeValues =
      array($linkTextAttributeValue, $linkUrlAttributeValue);
  $operation = new FeedItemOperation();
  $operation->operand = $item;
  $operation->operator = 'ADD';
  return $operation;
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
  AddSitelinksExample($user, $campaignId);
} catch (OAuth2Exception $e) {
  ExampleUtils::CheckForOAuth2Errors($e);
} catch (ValidationException $e) {
  ExampleUtils::CheckForOAuth2Errors($e);
} catch (Exception $e) {
  printf("An error has occurred: %s\n", $e->getMessage());
}
