@extends('templates.main')
@section('title', 'Editing User ' . $user->username)

@section('content')
<body class="edit-user">


    @include('templates.header')

    <div class="content-wrapper" ng-controller="editUserController" ng-strict-di>
        <h1>Editing Your Profile</h1>
        <main>
            <nav class="sticky-bar">
                <ul class="container">
                    <li class="gr-2"><a href="/users/{{ $user->username }}">Profile</a></li>
                    <li class="gr-2">Account</li>
                    <li class="gr-2">Email Notifications</li>
                    <li class="gr-2">Text/SMS Notifications</li>
                    <li class="gr-2">Reddit Notifications</li>
                </ul>
            </nav>
            <h2>Profile</h2>
            <section class="profile">
                <form>
                    <div class="gr-6">
                        <h3>You</h3>
                        <label for="summary">Write about yourself</label>
                        <textarea ng-model="profile.summary"></textarea>

                        <label for="twitter_account">Twitter</label>
                        <div class="prepended-input">
                            <span>@</span><input type="text" ng-model="profile.twitter_account" />
                        </div>

                        <label>Reddit</label>
                        <div class="prepended-input">
                            <span>/u/</span><input type="text" ng-model="profile.reddit_account" />
                        </div>
                    </div>

                    <div class="gr-6">
                        <h3>Favorites</h3>
                        <label>Favorite Mission</label>
                        <dropdown options="missions" has-default-option="true" unique-key="mission_id" title-key="name" searchable="true" ng-model="profile.favorite_mission"></dropdown>

                        <label>Favorite Mission Patch</label>
                        <dropdown options="patches" has-default-option="true" unique-key="mission_id" title-key="name" searchable="true" ng-model="profile.favorite_patch"></dropdown>

                        <label>Favorite Elon Musk Quote</label>
                        <textarea ng-model="profile.favorite_quote"></textarea>
                        <p>- Elon Musk.</p>
                    </div>

                    <!--<div class="gr-12">
                        <h3>Change Your Banner</h3>

                        <p>If you're a Mission Control subscriber, you can change your banner from the default blue to a custom image.</p>
                    </div>-->

                    <input type="submit" value="Update Profile" ng-click="updateProfile()" />
                </form>
            </section>

            <h2>Account</h2>
            <section class="account">
                <!-- Change password -->
                <!-- Buy More Mission Control -->
            </section>

            <h2>Email Notifications</h2>
            <section class="email-notifications">
                <p>You can turn on and off email notifications here.</p>

                <form>
                    <h3>Launch Change Notifications</h3>
                    <fieldset>
                        <legend>Notify me by email when...</legend>
                        <ul class="container">
                            <li class="gr-2">
                                <span>A launch time has changed</span>
                                <input type="checkbox" id="launchTimeChange" value="true" ng-model="emailNotifications.launchTimeChange" />
                                <label for="launchTimeChange"></label>
                            </li>
                            <li class="gr-2">
                                <span>When a new mission exists</span>
                                <input type="checkbox" id="newMission" value="true" ng-model="emailNotifications.newMission" />
                                <label for="newMission"></label>
                            </li>
                        </ul>
                    </fieldset>

                    @if(Auth::isSubscriber())
                        <h3>Upcoming Launch Notifications</h3>
                        <fieldset>
                            <legend>Notify me by email when...</legend>
                        </fieldset>
                        <ul class="container">
                            <li class="gr-2">
                                <span>There's a SpaceX launch in 24 hours</span>
                                <input type="checkbox" id="tMinus24HoursEmail" value="true" ng-model="emailNotifications.tMinus24HoursEmail" />
                                <label for="tMinus24HoursEmail"></label>
                            </li>
                            <li class="gr-2">
                                <span>There's a SpaceX launch in 3 hours</span>
                                <input type="checkbox" id="tMinus3HoursEmail" value="true" ng-model="emailNotifications.tMinus3HoursEmail" />
                                <label for="tMinus3HoursEmail"></label>
                            </li>
                            <li class="gr-2">
                                <span>There's a SpaceX launch in 1 hour</span>
                                <input type="checkbox" id="tMinus1HourEmail" value="true" ng-model="emailNotifications.tMinus1HourEmail" />
                                <label for="tMinus1HourEmail"></label>
                            </li>
                        </ul>

                        <h3>Other stuff</h3>
                        <fieldset>
                            <legend>Send me...</legend>
                            <ul class="container">
                                <li class="gr-2">
                                    <span>Monthly SpaceXStats News Summaries</span>
                                    <input type="checkbox" id="newsSummaries" value="true" ng-model="emailNotifications.newsSummaries" />
                                    <label for="newsSummaries"></label>
                                </li>
                            </ul>
                        </fieldset>
                    @endif
                    <input type="submit" ng-click="updateEmailNotifications()" value="Update Email Notifications" />
                </form>
            </section>

            <h2>Text/SMS Notifications</h2>
            <section class="text-sms-notifications">
                <p>Get upcoming launch notifications delivered directly to your mobile.</p>
                @if (Auth::isSubscriber())
                <form>
                    <label for="mobile">Enter your mobile number</label>
                    <input type="tel" id="mobile" ng-model="SMSNotification.mobile" placeholder="If you are outside the U.S., please include your country code." />

                    <p>How long before a launch would you like to recieve a notification?</p>

                    <input type="radio" name="status" ng-model="SMSNotification.status" id="off" value="false" />
                    <label for="off">Off</label>

                    <input type="radio" name="status" ng-model="SMSNotification.status" id="tMinus24HoursSMS" value="tMinus24HoursSMS" />
                    <label for="tMinus24HoursSMS">24 Hours Before</label>

                    <input type="radio" name="status" ng-model="SMSNotification.status" id="tMinus3HoursSMS" value="tMinus3HoursSMS" />
                    <label for="tMinus3HoursSMS">3 Hours Before</label>

                    <input type="radio" name="status" ng-model="SMSNotification.status" id="tMinus1HourSMS" value="tMinus1HourSMS" />
                    <label for="tMinus1HourSMS">1 Hour Before</label>

                    <input type="submit" ng-click="updateSMSNotifications()" value="Update SMS Notifications" />
                </form>
                @else
                    <p>Sign up for mission control to enable this feature!</p>
                @endif
            </section>

            <h2>Reddit Notifications</h2>
            <section class="reddit-notifications container">
                <div class="gr-6">
                    <h3>/r/SpaceX Notifications</h3>
                    <p>/r/SpaceX notifications allow you to automatically receive Reddit notifications about comments and posts with certain words made within the /r/SpaceX community via Personal Messages. Simply enter up to 10 trigger words (these are case insensitive) and select how frequently you would like to be notified.</p>
                </div>
                <div class="gr-6">
                    <h3>Redditwide Notifications</h3>
                    <p>Get notified by Reddit private message when threads are created across all of Reddit with certain keywords. Enter up to 10 trigger words (these are case insensitive) and select how frequently you would like to be notified.</p>
                </div>
            </section>
        </main>
    </div>
</body>
@stop

