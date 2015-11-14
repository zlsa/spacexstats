@extends('templates.main')
@section('title', 'Live')

@section('content')
    <body class="live" ng-controller="liveController" ng-strict-di>
    <!-- Custom Header -->
    <div class="content-wrapper">
        <header class="container">
            <p class="gr-2"><a href="/">SpaceX Stats</a></p>
            <h1 class="gr-8">@{{ liveParameters.pageTitle() }}</h1>
            @if (Auth::check())
                <p class="gr-2"><a target="_blank" href="/users/{{ Auth::user()->username }}">{{ Auth::user()->username }}</a></p>
            @else
                <p class="gr-2"><a target="_blank" href="/auth/login">My Account</a></p>
            @endif
        </header>
        <main>
            <!-- SpaceXStats live is not running -->
            @if ((Auth::check() && Auth::user()->isLaunchController()) || Auth::isAdmin())
                <section ng-if="isActive == false">
                    <span class="live-herotext">@{{ settings.getStartedHeroText }}</span>

                    <div ng-if="settings.isGettingStarted == false">
                        <button ng-click="settings.getStarted()">Get Started</button>
                    </div>

                    <form name="gettingStartedForm" ng-if="settings.isGettingStarted == true" novalidate>
                        <span>Is this for the upcoming launch or a miscellaneous event?</span>
                        <input type="checkbox" id="isForLaunch" name="isForLaunch" ng-model="liveParameters.isForLaunch" ng-change="liveParameters.toggleForLaunch()" />
                        <label for="isForLaunch"></label>
                        @{{ data.upcomingMission.name }}

                        <input type="text" ng-model="liveParameters.redditTitle" id="reddit-title" name="reddit-title" placeholder="The Reddit Thread Title" required />
                        <input type="text" ng-model="liveParameters.title" id="title" name="title" placeholder="The SpaceXStats Live title. Generally just the mission name." required />

                        <div ng-hide="liveParameters.isForLaunch">
                            <label for="countdownTo">Enter an event time (UTC)</label>
                            <datetime type="datetime" ng-model="liveParameters.countdownTo"></datetime>
                        </div>

                        <p>What streams should be shown?</p>
                        <span>SpaceX Stream</span>
                        <input type="checkbox" id="spacexstream" name="spacexstream" value="true" ng-model="liveParameters.streamingSources.spacex" />
                        <label for="spacexstream"></label>
                        <span>NASA Stream</span>
                        <input type="checkbox" id="nasastream" name="nasastream" value="true" ng-model="liveParameters.streamingSources.nasa" />
                        <label for="nasastream"></label>

                        <textarea ng-model="liveParameters.description" id="description" name="description" required
                                  placeholder="Write a small introduction about the launch here. 500 < chars, use markdown just like you would on Reddit. This is shown at the top of the Reddit thread.">
                        </textarea>

                        <h2>Sections</h2>
                        <p>Add sections of information to appear below the live updates.</p>
                        <div ng-repeat="section in liveParameters.sections">
                            <input type="text" ng-model="section.title" required /><button ng-click="settings.removeSection(section)">Remove</button>
                            <textarea ng-model="section.content" placeholder="The section content. You can again use markdown here." required></textarea>
                        </div>
                        <button ng-click="settings.addSection()">Add Section</button>

                        <h2>Content</h2>
                        <p>This section of content contains links to helpful content (The FAQ, hazard maps, previous launches, etc)</p>
                        <ul>
                            <li ng-repeat="resource in liveParameters.resources">
                                <input type="text" ng-model="resource.title" required placeholder="title of resource" />
                                <input type="text" ng-model="resource.url" required placeholder="URL to resource" />
                                <input type="text" ng-model="resource.courtesy" required placeholder="courtesy /r/spacex" />
                                <button ng-click="settings.removeResource(resource)">Remove</button>
                            </li>
                        </ul>
                        <button ng-click="settings.addResource()">Add Resource</button>

                        <input type="submit" ng-click="settings.turnOnSpaceXStatsLive()" ng-disabled="gettingStartedForm.$invalid || settings.isCreating" value="@{{ !settings.isCreating ? 'Create' : 'Creating...' }}" />
                    </form>
                </section>
            @endif

            <section ng-if="isActive == false && auth == false">
                <p class="exclaim">SpaceX Stats is not live at this time.</p>
            </section>

            <!-- If SpaceXStats live is running -->
            <nav class="in-page" ng-if="isActive == true">
                <ul>
                    <li></li>
                    <li>No video</li>
                    <li>SpaceX Only</li>
                    <li>NASA Only</li>
                    <li>Split-screen</li>

                    @if ((Auth::check() && Auth::user()->isLaunchController()) || Auth::isAdmin())
                        <li><i class="fa fa-cog"></i></li>
                    @endif
                </ul>
            </nav>

            <!-- add streams here -->

            <section ng-if="isActive == true">
                Resources, Sections, Maps, etc
            </section>

            @if ((Auth::check() && Auth::user()->isLaunchController()) || Auth::isAdmin())
                <section class="live-message-form" ng-if="isActive == true">
                    <form name="sendMessageForm">
                        <ul class="container">
                            <li class="gr-1">
                                <button ng-click="buttons.click('Hold/Abort')" ng-if="buttons.isVisible('Hold/Abort')">Hold/Abort</button>
                            </li>
                            <li class="gr-1">
                                <button ng-click="buttons.click('T-10s')" ng-if="buttons.isVisible('T-10s')">T-10s</button>
                            </li>
                            <li class="gr-1">
                                <button ng-click="buttons.click('Liftoff')" ng-if="buttons.isVisible('Liftoff')">Liftoff</button>
                            </li>
                            <li class="gr-1">
                                <button ng-click="buttons.click('Max-Q')" ng-if="buttons.isVisible('Max-Q')">Max-Q</button>
                            </li>
                            <li class="gr-1">
                                <button ng-click="buttons.click('MECO')" ng-if="buttons.isVisible('MECO')">MECO</button>
                            </li>
                            <li class="gr-1">
                                <button ng-click="buttons.click('MVac Ignition')" ng-if="buttons.isVisible('MVac Ignition')">MVac Ignition</button>
                            </li>
                            <li class="gr-1">
                                <button ng-click="buttons.click('SECO')" ng-if="buttons.isVisible('SECO')">SECO</button>
                            </li>
                            <li class="gr-1">
                                <button ng-click="buttons.click('Mission Success')" ng-if="buttons.isVisible('Mission Success')">Mission Success</button>
                            </li>
                            <li class="gr-1">
                                <button ng-click="buttons.click('Mission Failure')" ng-if="buttons.isVisible('Mission Failure')">Mission Failure</button>
                            </li>
                        </ul>
                        <textarea name="message" ng-model="send.new.message"
                                  placeholder="Enter a message here. Updates will be automatically timestamped, acronyms will be expanded, and tweets and images will be shown">required>
                        </textarea>
                        <input type="submit" ng-click="send.message()" value="Post" />
                    </form>
                </section>
            @endif

            <section ng-if="isActive == true">
                <div ng-repeat="update in updates">
                    <div>
                        <p><span>@{{ update.timestamp }}</span> @{{ update.created_at }}</p>
                        <i class="fa fa-edit" ng-if="auth == true"></i>
                    </div>

                    <div class="md">
                        @{{ update.update }}
                    </div>
                </div>
            </section>
        </main>
    </div>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/1.3.7/socket.io.js"></script>
    </body>
@stop