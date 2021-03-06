(function(module) {

    module.config([ '$httpProvider', function($httpProvider) {
        $httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
    } ]);

    module.controller('UploadController', function($scope, Upload) {

        $scope.uploadUrl = null;
        $scope.maxUploadSize = null;
        $scope.dropSupported = null;
        $scope.uploading = [ ];
        $scope.dropped = [ ];

        $scope.$watch('dropped', function(newValue) {
            $scope.uploadFiles(newValue);
        });

        $scope.uploadFiles = function (files) {
            if (files && files.length) {
                for (var i = 0; i < files.length; i++) {
                    var file = files[i];

                    if(file.size > $scope.maxUploadSize) {
                        file.progressPercentage = 100;
                        file.label = 'onii-chan y-your upload is t-too big…';
                        file.error = true;
                    }
                    else {
                        file.progressPercentage = 0;
                        file.label = null;

                        Upload.upload({
                            url: $scope.uploadUrl,
                            fields: {},
                            file: file
                        }).progress(function (evt) {
                            var progressPercentage = parseInt(100.0 * evt.loaded / evt.total);
                            evt.config.file.progressPercentage = progressPercentage;

                            if(progressPercentage === 100) {
                                evt.config.file.label = 'Finalising';
                            }
                        }).success(function (data, status, headers, config) {
                            if(data && data.files.length > 0) {
                                config.file.url = data.files[0].url;
                            }
                            else {
                                config.file.error = true;
                                config.file.label = 'Unknown upload error.';
                            }
                        }).error(function (data, status, headers, config) {
                            config.file.error = true;

                            if(data && data.error) {
                                config.file.label = data.error;
                            }
                            else {
                                config.file.label = 'Unknown upload error.';
                            }
                        });

                    }

                    $scope.uploading.push(file);
                }
            }
        };

    });

    module.filter('formatFileSize', function() {
        return function(size) {
            if(size == 0) {
                return '0';
            }

            var base = Math.log(size) / Math.log(1024);
            var suffixes = [ '', 'K', 'M', 'G', 'T' ];
            var suffix = suffixes[Math.floor(base)];
            return Math.round(Math.pow(1024, base - Math.floor(base))) + suffix;
        };
    });

})(angular.module('app', [ 'ngFileUpload' ]));