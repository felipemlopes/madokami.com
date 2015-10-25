@extends('layout')

@section('main')
    <div id="drop"
         ng-controller="UploadController"
         ng-init="uploadUrl = {{ json_encode(route('upload')) }};
                      maxUploadSize = {{ json_encode($maxUploadSize) }};"
         ngf-drop ng-model="dropped"
         ngf-drag-over-class="dragover"
         ngf-drop-available="dropSupported"
         ngf-multiple="true"
         ngf-allow-dir="true">

        <div class="ui center aligned page grid">
            <div class="sixteen wide column">
                <h1 class="ui header">Ohayou!</h1>

                <div class="ui large upload header">Max upload size is {{ $displayMaxUploadSize }}.</div>

                <noscript>
                    <div class="ui negative message">
                        <p>
                            <strong>Enable JavaScript</strong> you fucking autist neckbeard, it's not gonna hurt you
                        </p>
                    </div>
                </noscript>

                <a class="ui massive positive upload button" ngf-select="uploadFiles($files)"
                   ngf-multiple="true">
                    <span>Select <span ng-show="dropSupported">or drop</span> file(s)</span>
                </a>
            </div>
            <div class="eleven wide centered column" ng-show="uploading.length > 0" ng-cloak>
                <table class="ui very basic table">
                    <tbody>
                        <tr ng-repeat="file in uploading">
                            <td class="one wide">
                                <span>@{{ file.name }}</span>
                            </td>
                            <td class="one wide right aligned">
                                @{{ file.size | formatFileSize }}
                            </td>
                            <td class="right aligned">
                                <div class="ui progress" data-percent="@{{ file.progressPercentage }}"
                                     ng-class="{ success: ((file.progressPercentage === 100) && !file.error), error: file.error }"
                                     ng-hide="file.url">
                                    <div class="bar"
                                         style="transition-duration: 300ms; -webkit-transition-duration: 300ms;"
                                         ng-style="{ width: (file.progressPercentage + '%') }">
                                        <div class="progress" ng-if="!file.error">@{{ file.progressPercentage }}%</div>
                                        <div class="progress" ng-if="file.error">Error</div>
                                    </div>
                                    <div class="label" ng-show="file.label">@{{ file.label }}</div>
                                </div>

                                <a ng-href="@{{ file.url }}" target="_blank" ng-show="file.url">@{{ file.url }}</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="ui info message">
                <p>
                    All files are scanned by <a href="https://www.virustotal.com/" target="_blank" rel="nofollow">VirusTotal</a> on upload. If a file is infected it will be removed without further notice.
                </p>
            </div>
        </div>

        <footer id="footer">
            <nav id="footer-nav">
                <a href="https://github.com/kimoi/madokami.com" target="_blank"><i class="github icon"></i></a>
            </nav>
        </footer>
    </div>

    <style>
        body {
            background: url(/img/grills/{{ str_pad(mt_rand(1, 10), 2, '0', STR_PAD_LEFT) }}.png), url(img/bg.png);
            background-position: 85% 100%, top left;
            background-repeat: no-repeat, repeat;
            background-size: 25vh, auto;
        }
    </style>
@stop