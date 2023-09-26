<?php

namespace Careshop\CommunityCustomer\Helper;


use Magento\Framework\App\Helper\AbstractHelper;

/**
 *
 */
class Data extends AbstractHelper
{
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Config\Model\Config\Source\Locale\Timezone $timezone
    ) {
        parent::__construct($context);
        $this->timezone = $timezone;
        $this->_storeManager = $storeManager;
    }

    public function getListTimeZone()
    {
        return $this->timezone->toOptionArray();
    }

    public function getMediaUrl() {
        return  $this ->_storeManager-> getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
    }

    public function getListDefaultAvatar() {
        return array(
            '1'=>'avatars/default/1.jpg',
            '2'=>'avatars/default/2.jpg',
            '3'=>'avatars/default/3.png',
            '4'=>'avatars/default/4.png',
            '5'=>'avatars/default/5.jpg',
            '6'=>'avatars/default/6.jpg',
        );
    }

    public function getBoardLayoutOptions() {
        return array(
            '1' => __('Linear layout'),
            '2' => __('Theme layout'),
            '3' => __('Use default settings layout (Linear layout)'),
        );
    }
    public function getEmoticonOptions(){
        return array(
            'smile' => __('Smile'),
            'robot' => __('robot'),
            'man' => __('man'),
            'mrs' => __('Mrs'),
            'no' => __('No'),
            'none' => __('Use default settings(None)'),
        );
    }

    public function getFormatDateOptions(){
        return array(
            '1' => __('MM-dd-yyyy'),
            '2' => __('dd-mm-yyyy'),
            '3' => __('Possible value'),
            '4' => __('yyyy-MM-dd'),
            '0' => __('Use default settings(None)'),
        );
    }

    public function getShowDateOptions(){
        return array(
            '1' => __('Ratio data(eg three hours ago)'),
            '2' => __('Absolute dates(eg November 15, 1966)'),
            '0' => __('Use default settings(ratio data( eg, three hours ago))'),
        );
    }

    public function getListViewOptions(){
        return array(
            'grid' => __('Grid'),
            'ruse' => __('Ruse'),
            'feed' => __('Feed'),
            'photo' => __('Photo'),
            'default' => __('Use default settings(Grid)'),
        );
    }

    public function getListStyleOptions(){
        return array(
            'all' => __('All styles'),
            'forum' => __('Forum style only'),
            'possible' => __('Possible value'),
            'idea' => __('Idea style only'),
            'contest' => __('Contest style only'),
        );
    }

    public function getDefaultFlagTopicsOptions(){
        return array(
            'reading' => __('While reading'),
            'auto-label' => __('Automatically, after the auto-label period expires'),
            'auto-logout' => __('Automatically at logout'),
            'default' => __('Use defalt settings( When reading ) '),
        );
    }


    public function getDefaultSortTopicsOptions(){
        return array(
            'time-current' => __('Time of the current contribution'),
            'date-original' => __('Date of the original post'),
            'default' => __('Use defalt settings( Time of the current post ) '),
        );
    }

    public function getLenearSortTopicsOptions(){
        return array(
            'groups' => __('Groups'),
            'elder' => __('Elder first'),
            'newest' => __('Newest first'),
            'default' => __('Use defalt settings( oldest first ) '),
        );
    }

    public function getLenearTopicFilterOptions(){
        return array(
            'all' => __('All topics'),
            'discussion' => __('Only disccustions'),
            'questions-answers' => __('Only questions and answers'),
            'question-w-answers' => __('Questions with answers'),
            'question-0-ansers' => __('Question without answers'),
            'my-questions' => __('My questions'),
            'default' => __('Use defalt settings( oldest first ) '),
        );
    }

    public function getLenearSortQuestionsAnswersOptions(){
        return array(
            'newest' => __('Newest post'),
            'kudos' => __('Kudos'),
            'contribution' => __('contribution'),
            'number' => __('number'),
            'default' => __('Use defalt settings( Latest Post ) '),
        );
    }

    public function getThemeSortTopicsOptions(){
        return array(
            'elder' => __('Elder first'),
            'newest' => __('Newest first'),
            'default' => __('Use defalt settings( oldest first ) '),
        );
    }

    public function getPrivateMessagesOptions(){
        return array(
            'read-unread' => __('Read and unread messages'),
            'unread' => __('Only unread messages'),
            'default' => __('Use defalt settings( read and unread messages ) '),
        );
    }

    public function getMyFriendsMessagesOptions(){
        return array(
            'all' => __('All friends (online and offline)'),
            'online' => __('Only online friends'),
            'default' => __('Use defalt settings( All friends(online and ofline)) '),
        );
    }

    public function getPrivacyPersonOptions(){
        return array(
            'all' => __('All'),
            'only-friend' => __('Friend only'),
            'anyone' => __('Anyone'),
            'default' => __('Use defalt settings( Nobody )'),
        );
    }

    public function getPrivacyShowEmailOptions(){
        return array(
            'all' => __('All'),
            'only-friend' => __('Friend only'),
            'anyone' => __('Anyone'),
            'default' => __('Use defalt settings( Nobody )'),
        );
    }

    public function getPrivacyShowOnlineOptions(){
        return array(
            'all' => __('All'),
            'only-friend' => __('Friend only'),
            'anyone' => __('Anyone'),
            'default' => __('Use defalt settings( All )'),
        );
    }

    public function getPeriodLikeReceivedOptions(){
        return array(
            '24hours' => __('The past 24 hours'),
            '7days' => __('The past 7 days'),
            '30days' => __('The past 30 days'),
            '6months' => __('The past 6 months'),
            'last_year' => __('Last year'),
            'whole' => __('Whole Time - Include All Likes'),
            'default' => __('The past 7 days'),
        );
    }


    public function getPeriodLikeAuthorsOptions(){
        return array(
            '24hours' => __('The past 24 hours'),
            '7days' => __('The past 7 days'),
            '30days' => __('The past 30 days'),
            '6months' => __('The past 6 months'),
            'last_year' => __('Last year'),
            'whole' => __('Whole Time - Include All Likes'),
            'default' => __('The past 7 days'),
        );
    
    }
    public function getTaggingCloudOptions() {
        return array(
            'most' => __('Show most used tags'),
            'latest' => __('Show latest tags'),
            'my-tag' => __('Show my tags'),
            'default' => __('Use default settings(see most used tags'),
        );
    }

    public function getTaggingCloudAgeOptions() {
        return array(
            'most' => __('A day'),
            'latest' => __('One week'),
            'my-tag' => __('A month'),
            'default' => __('Six months'),
            'default' => __('A year'),
            'default' => __('All'),
            'default' => __('Use default settings(All)'),
        );
    }

    public function getEmailContentOptions() {
        return array(
            'sub-text' => __('Subject and text part'),
            'sub' => __('Only subject'),
            'default' => __('Use default settings(subject and body)'),
        );
    }

    public function getEmailFormatOptions() {
        return array(
            'auto' => __('Automatically'),
            'pure' => __('Pure text'),
            'html' => __('HTML'),
            'default' => __('Use default settings(Automatic)'),
        );
    }

    public function getAllPostOptions() {
        return array(
            1 => __('post article 1'),
            2 => __('post article 2'),
            3 => __('post article 3'),
            0 => __('Use default settings(All Posts)'),
        );
    }
    public function getMobilePicturesInPosts(){
        return array(
            1 => __('In line in the message text'),
            2 => __('Seperated from the message text'),
            0 => __('Use default settings(inline in message body)'),
        );

    }

    public function getEditorPostOptions(){
        return array(
            1 => __('Rich text Editor'),
            2 => __('Html editor'),
            0 => __('Use default settings(Rich text Editor)'),
        );

    }

    
}
