(function(module) {

    module.controller('UploadController', function($scope, Upload) {

        $scope.uploadUrl = null;
        $scope.uploading = [ ];


        $scope.uploadFiles = function (files) {
            if (files && files.length) {
                for (var i = 0; i < files.length; i++) {
                    var file = files[i];

                    file.progressPercentage = 0;

                    Upload.upload({
                        url: $scope.uploadUrl,
                        fields: { },
                        file: file
                    }).progress(function (evt) {
                        var progressPercentage = parseInt(100.0 * evt.loaded / evt.total);
                        console.log('progress: ' + progressPercentage + '% ' + evt.config.file.name);

                        evt.config.file.progressPercentage = progressPercentage;
                    }).success(function (data, status, headers, config) {
                        if(data && data.url) {
                            config.file.url = data.url;
                        }
                    }).error(function (data, status, headers, config) {
                        console.log('error status: ' + status, data, headers, config);
                    });

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