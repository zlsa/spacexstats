@extends('templates.main')
@section('title', 'Mission Control')

@section('content')
<body class="missioncontrol" ng-app="missionControlApp" ng-controller="missionControlController">

    @include('templates.header')

    <div class="content-wrapper">
        <h1>@{{ pageTitle }}</h1>
        <main>
            <div ng-controller="searchController">
                <form id="search-form">
                    <search></search>
                </form>
                <section ng-show="isCurrentlySearching">

                </section>
                <section ng-show="hasSearchResults">
                    <p>@{{ searchResults.hits.total }} results</p>
                    <p>Search took: @{{ searchResults.took }} ms</p>
                    <div ng-if="searchResults.hits.total == 0">
                        No results :(
                    </div>
                    <div ng-repeat="result in searchResults.hits.hits">
                        @{{ result._source.title }}
                    </div>
                </section>
            </div>

            <section ng-show="!hasSearchResults && !isCurrentlySearching">
                <div class="gr-8">
                    <h2>Uploads</h2>
                    <ul class="container">
                        <li class="gr-2">Latest</li>
                        <li class="gr-2">Hot</li>
                        <li class="gr-2">Discussions</li>
                        <li class="gr-2">From {{ $upcomingMission->name }}</li>
                    </ul>
                </div>

                <div class="gr-4">
                    <h2>Community Leaderboards</h2>
                    <ul class="container">
                        <li class="gr-3">Last Week</li>
                        <li class="gr-3">Last Month</li>
                        <li class="gr-3">Last Year</li>
                        <li class="gr-3">All Time</li>
                    </ul>
                </div>

                <div class="gr-6">
                    <h2>Recent Collections</h2>
                </div>

                <div class="gr-6">
                    <h2>Random Uploads</h2>
                </div>

                <div class="gr-4">
                    <h2>Recent Comments</h2>
                </div>

                <div class="gr-4">
                    <h2>Recent Favorites</h2>
                </div>

                <div class="gr-4">
                    <h2>Recent Downloads</h2>
                </div>
            </section>
        </main>
    </div>
</body>
@stop

