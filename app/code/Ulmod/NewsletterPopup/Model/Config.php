<?php
/**
 * Copyright © Ulmod. All rights reserved.
 * See LICENSE.txt for license details.
 */
 
namespace Ulmod\NewsletterPopup\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Cms\Model\Template\FilterProvider;

class Config
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var FilterProvider
     */
    private $filterProvider;

    /**
     * @param StoreManagerInterface $storeManager
     * @param FilterProvider $filterProvider
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        FilterProvider $filterProvider,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->storeManager = $storeManager;
        $this->filterProvider = $filterProvider;
        $this->scopeConfig = $scopeConfig;
    }
    
    /**
     * Get System Config values
     *
     * @return string|int|array|null
     */
    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Is module enabled or not
     *
     * @return string
     */
    public function isEnabled()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/general/is_enabled',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Get base url
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->storeManager->getStore()
            ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
    }


    /**
     * Get behavior
     *
     * @return int
     */
    public function getDisplayBehavior()
    {
        
        $behaviorConfig =  $this->scopeConfig->getValue(
            'newsletterpopup/display/behavior',
            ScopeInterface::SCOPE_STORE
        );
        
        $behavior = 2;
        if ($behaviorConfig == 1) {
            $behavior = 1;
        } elseif ($behaviorConfig == 2) {
            $behavior = 2;
        } else {
            $behavior = 3;
        }

        return $behavior;
    }
    
    /**
     * Get delay second
     *
     * @return string
     */
    public function getDelaySecond()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/display/second_delay',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get delay percent
     *
     * @return string
     */
    public function getDelayPercent()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/display/percent_delay',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Is show only once or not
     *
     * @return string
     */
    public function getCookieLifetime()
    {
        return (int)$this->scopeConfig->getValue(
            'newsletterpopup/display/cookie_lifetime',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get delay config value
     * @return string
     */
    public function getDelay()
    {
        return   $this->scopeConfig->getValue(
            'newsletterpopup/display/delay',
            ScopeInterface::SCOPE_STORE
        );
    }
 
    /**
     * Get popup title config value
     * @return string
     */
    public function getPopupTitle()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/display/popup_title',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Get content message config value
     * @return string
     */
    public function getContentMsgConfig()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/display/content_msg',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get content message
     * @return string
     */
    public function getContentMsg()
    {
        $htmlContent = $this->filterProvider->getBlockFilter()
            ->filter($this->getContentMsgConfig());
        
        return $htmlContent;
    }

    /**
     * Is show terms
     * @return bool
     */
    public function isShowTermCondition()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/display/show_terms_condition',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Get terms and condition message
     * @return string
     */
    public function getTermConditionMsg()
    {
        $termConfigValue = $this->scopeConfig->getValue(
            'newsletterpopup/display/terms_condition',
            ScopeInterface::SCOPE_STORE
        );
        
        $termHhtmlContent = $this->filterProvider->getBlockFilter()
            ->filter($termConfigValue);
        
        return $termHhtmlContent;
    }
    
    /**
     * Get subscribe btntext config value
     * @return string
     */
    public function getSubscribeBtnText()
    {
        return   $this->scopeConfig->getValue(
            'newsletterpopup/display/subscribe_btntext',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Ceck if social media buttons is enabled
     *
     * @return string
     */
    public function isSocialButtonsEnabled()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/enable_social_buttons',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Get facebook status config value
     * @return int
     */
    public function getFacebookStatus()
    {
        return  $this->scopeConfig->getValue(
            'newsletterpopup/buttons/enable_facebook',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Get facebook url config value
     * @return string
     */
    public function getFacebookUrl()
    {
        return  $this->scopeConfig->getValue(
            'newsletterpopup/buttons/facebookurl',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Get facebook title config value
     * @return string
     */
    public function getFacebookTitle()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/facebooktitle',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Get youtube status config value
     * @return int
     */
    public function getYoutubeStatus()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/enable_youtube',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Get youtube url config value
     * @return string
     */
    public function getYoutubeUrl()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/youtubeurl',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get youtube title config value
     * @return string
     */
    public function getYoutubeTitle()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/youtubetitle',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get twitter status config value
     * @return int
     */
    public function getTwitterStatus()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/enable_twitter',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Get twitter url config value
     * @return string
     */
    public function getTwitterUrl()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/twitterurl',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Get twitter title config value
     * @return string
     */
    public function getTwitterTitle()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/twittertitle',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get linkedin status config value
     * @return int
     */
    public function getLinkedinStatus()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/enable_linkedin',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Get linkedin url config value
     * @return string
     */
    public function getLinkedinUrl()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/linkedinurl',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Get linkedin title config value
     * @return string
     */
    public function getLinkedinTitle()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/linkedintitle',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get pinterest status config value
     * @return int
     */
    public function getPinterestStatus()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/enable_pinterest',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Get pinterest url config value
     * @return string
     */
    public function getPinterestUrl()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/pinteresturl',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get pinterest title config value
     * @return string
     */
    public function getPinterestTitle()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/pinteresttitle',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get google status config value
     * @return int
     */
    public function getGoogleStatus()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/enable_googleplus',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Get google url config value
     * @return string
     */
    public function getGoogleUrl()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/googleplusturl',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Get google title config value
     * @return string
     */
    public function getGoogleTitle()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/googleplustitle',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get tumblr status config value
     * @return int
     */
    public function getTumblrStatus()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/enable_tumblr',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Get tumblr url config value
     * @return string
     */
    public function getTumblrUrl()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/tumblrurl',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Get tumblr title config value
     * @return string
     */
    public function getTumblrTitle()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/tumblrtitle',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Get instagram status config value
     * @return int
     */
    public function getInstagramStatus()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/enable_instagram',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Get instagram url config value
     * @return string
     */
    public function getInstagramUrl()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/instagramurl',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Get instagram title config value
     * @return string
     */
    public function getInstagramTitle()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/instagramtitle',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Get reddit status config value
     * @return int
     */
    public function getRedditStatus()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/enable_reddit',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Get reddit url config value
     * @return string
     */
    public function getRedditUrl()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/redditurl',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get reddit title config value
     * @return string
     */
    public function getRedditTitle()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/reddittitle',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Get vk status config value
     * @return int
     */
    public function getVkStatus()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/enable_vk',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Get vk url config value
     * @return string
     */
    public function getVkUrl()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/vkurl',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get vk title config value
     * @return string
     */
    public function getVkTitle()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/vktitle',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get flickr status config value
     * @return int
     */
    public function getFlickrStatus()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/enable_flickr',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Get flickr url config value
     * @return string
     */
    public function getFlickrUrl()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/flickrurl',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Get flickr title config value
     * @return string
     */
    public function getFlickrTitle()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/flickrtitle',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Get vine status config value
     * @return int
     */
    public function getVineStatus()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/enable_vine',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Get vine url config value
     * @return string
     */
    public function getVineUrl()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/vineurl',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Get vine title config value
     * @return string
     */
    public function getVineTitle()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/vine/title',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get vimeo status config value
     * @return int
     */
    public function getVimeoStatus()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/enable_vimeo',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Get vimeo url config value
     * @return string
     */
    public function getVimeoUrl()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/vimeourl',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Get vimeo title config value
     * @return string
     */
    public function getVimeoTitle()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/vimeotitle',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get foursquare status config value
     * @return int
     */
    public function getFoursquareStatus()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/enable_foursquare',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Get foursquare url config value
     * @return string
     */
    public function getFoursquareUrl()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/foursquareurl',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Get foursquare title config value
     * @return string
     */
    public function getFoursquareTitle()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/foursquaretitle',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get rss status config value
     * @return int
     */
    public function getRssStatus()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/enable_rss',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Get rss url config value
     * @return string
     */
    public function getRssUrl()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/rssurl',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Get rss title config value
     * @return string
     */
    public function getRssTitle()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/rsstitle',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Get mail status config value
     * @return int
     */
    public function getMailStatus()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/enable_mail',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Get mail url config value
     * @return string
     */
    public function getMailUrl()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/mailurl',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get mail title config value
     * @return string
     */
    public function getMailTitle()
    {
        return $this->scopeConfig->getValue(
            'newsletterpopup/buttons/mailtitle',
            ScopeInterface::SCOPE_STORE
        );
    }
}
