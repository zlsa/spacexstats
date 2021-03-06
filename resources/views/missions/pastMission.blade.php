@extends('templates.main')
@section('title', $mission->name)

@section('content')
<body class="past-mission" ng-controller="pastMissionController" ng-strict-di>

    @include('templates.header', array('backgroundImage' => !is_null($mission->featuredImage) ? $mission->featuredImage->media : ''))

    <div class="content-wrapper">
        <h1>{{ $mission->name }}</h1>
        <main>
            <nav class="in-page sticky-bar">
                <ul class="container">
                    <li class="gr-1">
                        <a href="#article">Article</a>
                    </li>
                    <li class="gr-1">
                        <a href="#details">Details</a>
                    </li>
                    <li class="gr-1">
                        <a href="#images">Images</a>
                    </li>
                    <li class="gr-1">
                        <a href="#videos">Videos</a>
                    </li>
                    <li class="gr-1">
                        <a href="#documents">Documents</a>
                    </li>
                    <li class="gr-1">
                        <a href="#articles">Articles</a>
                    </li>
                    <li class="gr-1">
                        <a href="#timeline">Timeline</a>
                    </li>
                    <li class="gr-1">
                        <a href="#analytics">Analytics</a>
                    </li>
                    @if (Auth::isAdmin())
                        <li class="gr-1 actions">
                            <a class="link" href="/missions/{{ $mission->slug }}/edit"><i class="fa fa-pencil"></i></a>
                        </li>
                    @endif
                    <li class="gr-1 float-right">
                        <span class="status complete"><i class="fa fa-flag"></i> {{ $mission->status }}</span>
                    </li>
                    <li class="gr-1 float-right">
                        @if ($mission->outcome == 'Success')
                            <span class="outcome success"><i class="fa fa-check"></i> Success</span>
                        @else
                            <span class="outcome failure"><i class="fa fa-cross"></i> Failure</span>
                        @endif
                    </li>
                </ul>
            </nav>

            <section class="highlights">
                @if(isset($pastMission))
                    <a href="/missions/{{ $pastMission->slug }}">
                        <div class="mission-link past-mission-link">
                            <span class="placeholder">Previous Mission</span>
                            <span class="link"><i class="fa fa-arrow-left"></i> {{ $pastMission->name }}</span>
                        </div>
                    </a>
                @endif
                @if(isset($futureMission))
                    <a href="/missions/{{ $futureMission->slug }}">
                        <div class="mission-link future-mission-link">
                            <span class="link">{{ $futureMission->name }} <i class="fa fa-arrow-right"></i></span>
                            <span class="placeholder">Next Mission</span>
                        </div>
                    </a>
                @endif
            </section>

            {!! $mission->present()->article() !!}

            <h2>Details</h2>
            <section id="details" class="scrollto">
                @include('templates.missionCard', ['size' => 'large', 'mission' => $mission])
                <div class="gr-8">
                    <h3>Flight Details</h3>
                    <mission-profile></mission-profile>

                    @if(count($mission->spacecraftFlight))
                        <h3>{{ $mission->spacecraftFlight->spacecraft->name }}</h3>
                        @include('templates.spacecraftCard')
                    @endif
                    <h3>Satellites</h3>
                    <h3>Upper Stage</h3>
                    In Orbit / Deorbited / Decayed / Did Not Reach Oribt

                    Time in Orbit (countup)

                    Current Orbit (646km x 321km, inclined 9.4deg)
                </div>
                <div class="gr-4">
                    <h3>Library</h3>
                    <ul class="library">

                        <li id="launch-video">
                            <span>Watch the Launch</span>
                        </li>

                        @if($mission->missionPatch()->count() == 1)
                            <li id="mission-patch">
                                <img src="{{ $mission->missionPatch->thumb_small }}"/>
                                <span>{{ $mission->name }} Mission Patch</span>
                            </li>
                        @endif

                        <li id="press-kit">
                            <span>Press Kit</span>
                        </li>

                        @if($mission->spacecraftFlight()->count() == 1)
                            <li id="cargo-manifest">
                                <span>Cargo Manifest</span>
                            </li>
                        @endif

                        <li id="prelaunch-press-conference">
                            <span>Prelaunch Press Conference</span>
                        </li>

                        <li id="postlaunch-press-conference">
                            <span>Postlaunch Press Conference</span>
                        </li>

                        @if ($mission->reddit_discussion != null)
                            <li id="reddit-discussion">
                                <span>/r/SpaceX Reddit Live Thread</span>
                            </li>
                        @endif

                        @if ($mission->flightclub != null)
                            <li id="flightclub-link">
                                <span>FlightClub Simulation</span>
                            </li>
                        @endif

                        @if (Auth::isMember())
                            <li id="raw-data-download">
                                <span><a href="/missions/{{ $mission->slug }}/raw">Raw Data Download</a></span>
                            </li>

                            <li id="mission-collection">
                                <span>{{ $mission->name }} Mission Collection</span>
                            </li>
                        @endif
                    </ul>
                </div>
            </section>

            <h2>Images</h2>
            <section id="images" class="scrollto">
                @if ($imageCount > 0)
                    @foreach ($images as $image)
                        <div class="square">
                            <img src="" alt="" class="square" />
                        </div>
                    @endforeach
                    @if ($imageCount > 20)
                        <div class="square">
                            {{ $imageCount - 20 }} more...
                        </div>
                    @endif
                @endif
            </section>

            <h2>Videos</h2>
            <section id="videos" class="scrollto container">
                @if ($mission->launch_video != null)
                    <div class="gr-8">
                        <h3>Launch Video</h3>
                    </div>
                @endif
                <div class="gr-4 {{ $mission->launch_video != null ? 'launch-video' : 'no-launch-video' }}">

                </div>

            </section>

            <h2>Documents</h2>
            <section id="documents" class="scrollto">
                @foreach($documents as $document)
                @endforeach
            </section>

            <h2>Articles</h2>
            <section id="articles" class="scrollto">
                @foreach ($mission->articles() as $article)
                @endforeach
            </section>

            <h2>Timeline</h2>
            <section id="timeline" class="scrollto">
                <h3>Prelaunch</h3>
                    <table>
                        <tr>
                            <th>Occurred At</th>
                            <th>Event Type</th>
                            <th>Summary</th>
                            <th>Scheduled Lauch at time of event</th>
                        </tr>
                        @foreach ($mission->prelaunchEvents as $prelaunchEvent)
                            <tr>
                                <td>{{ $prelaunchEvent->occurred_at }}</td>
                                <td>{{ $prelaunchEvent->event }}</td>
                                <td>{{ $prelaunchEvent->summary }}</td>
                                <td>{{ $prelaunchEvent->scheduled_launch_date_time }}</td>
                            </tr>
                        @endforeach
                    </table>

                <h3>Launch</h3>
                    @if ($mission->telemetries->count() > 0)
                        <p>The following data represents telemetry and readouts from the countdown net & webcast at SpaceX's Hawthorne HQ.</p>
                        <table class="data-table">
                            <tr>
                                <th>Timestamp</th>
                                <th>Readout</th>
                            </tr>
                            @foreach($mission->telemetries as $telemetry)
                                <tr>
                                    <td>{{ $telemetry->formatted_timestamp }}</td>
                                    <td>{{ $telemetry->readout }}</td>
                                </tr>
                            @endforeach
                        </table>
                    @else
                        <p class="exclaim">No telemetry yet!</p>
                    @endif
                <h3>Postlaunch</h3>
            </section>

            <h2>Analytics</h2>
            <section id="analytics" class="scrollto">
                @if(Auth::isSubscriber())
                    <h3>Dataplots</h3>
                    <p>These dataplots are based on kinematic data extracted from the countdown net during launch, and are only approximate. For more detailed simulations, refer to the FlightClub entry for this launch.</p>
                    <ul class="container">
                        <li class="gr-4 gr-12@small">
                            <chart class="dataplot" data="altitudeVsTime.data" settings="altitudeVsTime.settings" width="100%" height="400px"></chart>
                        </li>
                        <li class="gr-4 gr-12@small">
                            <chart class="dataplot" data="velocityVsTime.data" settings="velocityVsTime.settings" width="100%" height="400px"></chart>
                        </li>
                        <li class="gr-4 gr-12@small">
                            <chart class="dataplot" data="downrangeVsTime.data" settings="downrangeVsTime.settings" width="100%" height="400px"></chart>
                        </li>
                        <li class="gr-4 gr-12@small">
                            <chart class="dataplot" data="altitudeVsDownrange.data" settings="altitudeVsDownrange.settings" width="100%" height="400px"></chart>
                        </li>
                    </ul>

                    <h3>Interpolation Queries</h3>

                    <h3>Upper Stage</h3>
                    @if ($mission->orbitalElements->count() != 0)
                        {{ $orbitalElements->first()->perigee }}km x {{ $orbitalElements->first()->apogee }}km, inclined {{ $orbitalElements->first()->inclination }}deg

                        <h4>Latest TLE</h4>
                        <div class="tle">
                            <p>{{ $orbitalElements->first()->object_name }}</p>
                        </div>

                        <h4>Last 5 Orbital Elements</h4>
                        <table>
                            <tr>
                                <th>Epoch</th>
                                <th>Perigee</th>
                                <th>Apogee</th>
                                <th>Inclination</th>
                                <th>Eccentricity</th>
                                <th>Semimajor Axis</th>
                                <th>Orbital Period</th>
                            </tr>
                        </table>
                    @else
                        <p class="exclaim">No orbital element data at this time.</p>
                    @endif

                    <h3>Maps</h3>
                @else
                    <p class="should-subscribe exclaim">Subscribe to Mission Control to see mission analytics.</p>
                @endif
            </section>
        </main>
    </div>
</body>
@stop