(function() {
    var app = angular.module('app', []);

    app.controller("futureMissionController", ['$http', '$scope', '$filter', 'flashMessage', function($http, $scope, $filter, flashMessage) {

        $scope.missionSlug = laravel.mission.slug;
        $scope.launchDateTime = moment(laravel.mission.launch_date_time).toDate();
        $scope.launchSpecificity = laravel.mission.launch_specificity;

        $scope.$watch("launchSpecificity", function(newValue) {
            $scope.isLaunchExact =  (newValue == 6 || newValue == 7);
        });

        $scope.$watchCollection('[isLaunchExact, launchDateTime]', function(newValues) {
            if (newValues[0] === true) {
                $scope.launchUnixSeconds =  (moment(newValues[1]).unix());
            }
            $scope.launchUnixSeconds =  null;
        });

        $scope.lastRequest = moment().unix();
        $scope.secondsSinceLastRequest = 0;

        $scope.secondsToLaunch;

        $scope.requestFrequencyManager = function() {
            $scope.secondsSinceLastRequest = Math.floor($.now() / 1000) - $scope.lastRequest;
            $scope.secondsToLaunch = $scope.launchUnixSeconds - Math.floor($.now() / 1000);

            /*
             Make requests to the server for launchdatetime and webcast updates at the following frequencies:
             >24hrs to launch    =   1hr / request
             1hr-24hrs           =   15min / request
             20min-1hr           =   5 min / request
             <20min              =   30sec / request
             */
            var aRequestNeedsToBeMade = ($scope.secondsToLaunch >= 86400 && $scope.secondsSinceLastRequest >= 3600) ||
                ($scope.secondsToLaunch >= 3600 && $scope.secondsToLaunch < 86400 && $scope.secondsSinceLastRequest >= 900) ||
                ($scope.secondsToLaunch >= 1200 && $scope.secondsToLaunch < 3600 && $scope.secondsSinceLastRequest >= 300) ||
                ($scope.secondsToLaunch < 1200 && $scope.secondsSinceLastRequest >= 30);

            if (aRequestNeedsToBeMade === true) {
                // Make both requests then update the time since last request
                $scope.requestLaunchDateTime();
                $scope.requestWebcastStatus();
                $scope.lastRequest = moment().unix();
            }
        }

        $scope.requestLaunchDateTime = function() {
            $http.get('/missions/' + $scope.missionSlug + '/requestlaunchdatetime')
                .then(function(response) {
                    // If there has been a change in the launch datetime, update
                    if ($scope.launchDateTime !== response.data.launchDateTime) {
                        $scope.launchDateTime = response.data.launchDateTime;
                        $scope.launchSpecificity = response.data.launchSpecificity;

                        flashMessage.addOK('Launch time updated!');
                    }
                });
        }

        $scope.requestWebcastStatus = function() {
            $http.get('/webcast/getstatus')
                .then(function(response) {
                    $scope.webcast.isLive = response.data.isLive;
                    $scope.webcast.viewers = response.data.viewers;
                });
        }

        $scope.webcast = {
            isLive: laravel.webcast.isLive,
            viewers: laravel.webcast.viewers
        }

        $scope.$watchCollection('[webcast.isLive, secondsToLaunch]', function(newValues) {
            if (newValues[1] < (60 * 60 * 24) && newValues[0] == 'true') {
                $scope.webcast.status = 'webcast-live';
            } else if (newValues[1] < (60 * 60 * 24) && newValues[0] == 'false') {
                $scope.webcast.status = 'webcast-updates';
            } else {
                $scope.webcast.status = 'webcast-inactive';
            }
        });

        $scope.$watch('webcast.status', function(newValue) {
            if (newValue === 'webcast-live') {
                $scope.webcast.publicStatus = 'Live Webcast'
            } else if (newValue === 'webcast-updates') {
                $scope.webcast.publicStatus = 'Launch Updates'
            }
        });

        $scope.$watch('webcast.viewers', function(newValue) {
            $scope.webcast.publicViewers = ' (' + newValue + ' viewers)';
        });

        /*
        *   Timezone stuff.
         */
        // Get the IANA Timezone identifier and format it into a 3 letter timezone.
        $scope.localTimezone = moment().tz(jstz.determine().name()).format('z');
        $scope.currentFormat = 'h:mm:ssa MMMM d, yyyy';
        $scope.currentTimezone;
        $scope.currentTimezoneFormatted = "Local ("+ $scope.localTimezone +")";

        $scope.setTimezone = function(timezoneToSet) {
            if (timezoneToSet === 'local') {
                $scope.currentTimezone = null;
                $scope.currentTimezoneFormatted = "Local ("+ $scope.localTimezone +")";
            } else if (timezoneToSet === 'ET') {
                $scope.currentTimezone = moment().tz("America/New_York").format('z');
                $scope.currentTimezoneFormatted = 'Eastern';
            } else if (timezoneToSet === 'PT') {
                $scope.currentTimezone = moment().tz("America/Los_Angeles").format('z');
                $scope.currentTimezoneFormatted = 'Pacific';
            } else {
                $scope.currentTimezoneFormatted = $scope.currentTimezone = 'UTC';
            }
        };

        $scope.displayDateTime = function() {
            if ($scope.isLaunchExact) {
                return $filter('date')($scope.launchDateTime, $scope.currentFormat, $scope.currentTimezone);
            } else {
                return $scope.launchDateTime;
            }

        };
    }]);
})();