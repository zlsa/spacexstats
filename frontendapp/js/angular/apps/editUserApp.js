angular.module("editUserApp", ["directives.selectList"], ['$interpolateProvider', function($interpolateProvider) {
    $interpolateProvider.startSymbol('[[');
    $interpolateProvider.endSymbol(']]');

}]).controller("editUserController", ['$http', '$scope', function($http, $scope) {

    $scope.username = laravel.user.username;

    $scope.missions = laravel.missions;

    $scope.profile = {
        summary: laravel.user.profile.summary,
        twitter_account: laravel.user.profile.twitter_account,
        reddit_account: laravel.user.profile.reddit_account,
        favorite_quote: laravel.user.profile.favorite_quote,
        favorite_mission: laravel.user.profile.favorite_mission,
        favorite_patch: laravel.user.profile.favorite_patch
    };

    $scope.updateProfile = function() {
        $http.post('/users/' + $scope.username + '/edit/profile', $scope.profile)
            .then(function(response) {
                 console.log(response);
            });
    }

    $scope.emailNotifications = {
        launchTimeChange: laravel.notifications.launchTimeChange,
        newMission: laravel.notifications.newMission,
        tMinus24HoursEmail: laravel.notifications.tMinus24HoursEmail,
        tMinus3HoursEmail: laravel.notifications.tMinus3HoursEmail,
        tMinus1HourEmail: laravel.notifications.tMinus1HourEmail,
        newsSummaries: laravel.notifications.newsSummaries
    }

    $scope.updateEmailNotifications = function() {
        console.log(laravel);
        console.log($scope.emailNotifications);

        $http.post('/users/' + $scope.username + '/edit/emailnotifications',
            { 'emailNotifications': $scope.emailNotifications }
        )
            .then(function(response) {
                console.log(response);
            });
    }

    $scope.SMSNotification = {
        mobile: laravel.user.mobile
    }

    if (laravel.notifications.tMinus24HoursSMS === true) {
        $scope.SMSNotification.status = "tMinus24HoursSMS";
    } else if (laravel.notifications.tMinus3HoursSMS === true) {
        $scope.SMSNotification.status = "tMinus3HoursSMS";
    } else if (laravel.notifications.tMinus1HourSMS === true) {
        $scope.SMSNotification.status = "tMinus1HourSMS";
    } else {
        $scope.SMSNotification.status = null;
    }

    $scope.$watch('SMSNotification.status', function() {
        console.log('yay');
    })

    $scope.updateSMSNotifications = function() {
        $http.post('/users/' + $scope.username + '/edit/smsnotifications',
            { 'SMSNotification': $scope.SMSNotification }
        )
            .then(function(response) {
                console.log(response);
            });
    }

}]);
