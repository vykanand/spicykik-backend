var app = angular.module('MTapp', ['ui.bootstrap']);

app.filter('startFrom', function() {
    return function(input, start) {
        if (input) {
            start = Number(start); //parse to int
            return input.slice(start);
        }
        return [];
    }
});

app.filter('vla', function() {
    return function(str) {
        var i, frags = str.split('_');
        for (i = 0; i < frags.length; i++) {
            frags[i] = frags[i].charAt(0).toUpperCase() + frags[i].slice(1);
        }
        return frags.join(' ');
    };
});

app.controller('mtctrl', function($scope, $http, $location, $uibModal, $q) {

    // console.log(localStorage.getItem("apikey"));
    $scope.url = 'api.php';
    var passphrase = "yug";


    $scope.humanize = function(str) {
        var i, frags = str.split('_');
        for (i = 0; i < frags.length; i++) {
            frags[i] = frags[i].charAt(0).toUpperCase() + frags[i].slice(1);
        }
        return frags.join(' ');
    }

    function removefromArray(arr) {
        var what, a = arguments,
            L = a.length,
            ax;
        while (L > 1 && arr.length) {
            what = a[--L];
            while ((ax = arr.indexOf(what)) !== -1) {
                arr.splice(ax, 1);
            }
        }
        return arr;
    }

    $scope.setPage = function(pageNo) {
        $scope.currentPage = pageNo;
    };
    $scope.filter = function() {
        $timeout(function() {
            $scope.filteredItems = $scope.list.length;
        }, 10);
    };
    $scope.sort_by = function(predicate) {
        $scope.predicate = predicate;
        $scope.reverse = !$scope.reverse;
    };

    $scope.trans = function(obj) {
        var str = [];
        for (var p in obj)
            str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
        return str.join("&");
    }


    $scope.login = function(user, pass) {

        var str = pass;

        var regex = new RegExp(passphrase, 'g');
        var resx = str.match(regex);
        var pass = 0
        if (resx) {
            if (resx.length > 0) {
                var pass = 1
                window.location.href = "adm.html";
                console.log('found');
            } else {
                console.log('notfound');
            }
        } else {
            // notie.alert({ type: 'error', text: '', stay: false })
            window.top.postMessage('error^Wrong password', '*')
            // alert("Wrong password");
            window.location.href = "index.html";
        }


    }

    $scope.cancelbt = function() {
        $scope.modalInstance.dismiss('cancel');
        document.getElementById('mainsection').classList.remove("blurcontent");
    };



    $scope.flfl = function() {
        // console.log($scope.filtered);
        var pp = []
        for (let i = 0; i < $scope.filtered.length; i++) {
            pp.push($scope.filtered[i].id)
        }
        // console.log(pp);
        $http.get($scope.url + '?deliid=true&iid=' + pp).success(function(data) {
            console.log(data);
        })
    }


   $scope.userole = function(ddt) {
       
       if(ddt.type == 'admin'){
           window.top.postMessage('success^Admin has all the access by default', '*')
       }else{
       localStorage.setItem('tmprol',JSON.stringify(ddt))
       window.location.href = 'userole.php'
       }
       
   } 


    $scope.admin = function() {



        $http.get($scope.url + '?getcontent=true').success(function(data) {
            console.log(data);

            localStorage.setItem('jjj', JSON.stringify(data))
            // $scope.humanize
            $scope.list = data;

            if(data.length > 0){

            $scope.showdat = Object.keys($scope.list[0]);
            for (var i = 0; i < $scope.showdat.length; i++) {
                $scope.showdat[i] = $scope.humanize($scope.showdat[i])
            }

            // delete $scope.list.created_at;
            $scope.fields = Object.keys($scope.list[0]);



            removefromArray($scope.fields, "created_at");

            
            
$http.get($scope.url + '?getfirstcontent=true').success(function(data) {
                     console.log(data);
                     $scope.addingNew = Object.values(data)
                window.top.postMessage('responseact', '*')
            });
            

            for (var i = 0; i < $scope.fields.length; i++) {
                $scope.fields[i] = $scope.humanize($scope.fields[i])
            }

            console.log('fields', $scope.fields);
            $scope.idun = data[0].id;
            $scope.currentPage = 1; //current page
            $scope.entryLimit = 5; //max no of items to display in a page
            $scope.filteredItems = $scope.list.length; //Initially for no filter S





            }else{
                console.log('nodata');

                 $http.get($scope.url + '?getfirstcontent=true').success(function(data) {
                     console.log(data);
                     $scope.addingNew = Object.values(data)
                $scope.addproduct()
                window.top.postMessage('responseact', '*')
            });
            }




        });
    }




    var amodalPopup = function() {
        document.getElementById('mainsection').classList.add("blurcontent");

        return $scope.modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'blocks/modal/create.html',
            scope: $scope
        });
    };


    $scope.addproduct = function() {

        amodalPopup().result
            .then(function(data) {

            })
            .then(null, function(reason) {
                document.getElementById('mainsection').classList.remove("blurcontent");

            });
    }



    $scope.crtpag = function(vx) {
        var addata = {};
        $('#adtfrm').find('input').each(function() {
            addata[this.name] = $(this).val();
        });
        addata[vx] = true;
        console.log("addata", addata);
        var url = $scope.url
        //post req start      
        $http({
            method: 'POST',
            url: url,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            transformRequest: function(obj) {
                var str = [];
                for (var p in obj)
                    str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                return str.join("&");
            },
            data: addata
        }).success(function(response) {
            console.log(response);
            window.top.postMessage('success^Created Successfully', '*')
            // notie.alert({ type: 'success', text: 'Created Successfully', stay: false })
            // alert('Created successfully');
            window.location.reload();
        }).catch(function onError(response) {
            window.top.postMessage('error^Some Error Occured', '*')
            // notie.alert({ type: 'error', text: 'Some Error Occured', stay: false })
            // alert('Some error occured');
            window.location.reload();
        });
        //post req ends
    }


    $scope.eee = function() {
        function JSON2CSV(objArray) {
            var array = typeof objArray != 'object' ? JSON.parse(objArray) : objArray;
            var str = '';
            var line = '';

            if ($("#labels").is(':checked')) {
                var head = array[0];
                if ($("#quote").is(':checked')) {
                    for (var index in array[0]) {
                        var value = String(index);
                        line += '"' + value.replace(/"/g, '""') + '",';
                    }
                } else {
                    for (var index in array[0]) {
                        line += index + ',';
                    }
                }

                line = line.slice(0, -1);
                str += line + '\r\n';
            }

            for (var i = 0; i < array.length; i++) {
                var line = '';

                if ($("#quote").is(':checked')) {
                    for (var index in array[i]) {
                        var value = String(array[i][index]);
                        line += '"' + value.replace(/"/g, '""') + '",';
                    }
                } else {
                    for (var index in array[i]) {
                        line += array[i][index] + ',';
                    }
                }

                line = line.slice(0, -1);
                str += line + '\r\n';
            }
            return str;
        }

        var json_pre = localStorage.getItem('csvs')

        console.log(json_pre);
        var json = $.parseJSON(json_pre);

        var csv = JSON2CSV(json);
        var downloadLink = document.createElement("a");
        var blob = new Blob(["\ufeff", csv]);
        var url = URL.createObjectURL(blob);
        downloadLink.href = url;
        downloadLink.download = "data.csv";

        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
    }

    $scope.edityes = function(vx, id) {

        var eddata = {};
        $('#edtfrm').find('input').each(function() {

            if ($(this).val()) {
                var tyt = this.name
                var uone = tyt.toLowerCase();
                var utwo = uone.replace(/ /g, "_");
                eddata[utwo] = $(this).val();
            } else {
                var posx = this.name
                var oswnm = posx.toLowerCase();
                var ytt = oswnm.replace(/ /g, "_");
                eddata[ytt] = this.placeholder;
            }

        });

        eddata['id'] = $scope.edtid;
        eddata[vx] = true;
        console.log("eddata", eddata);

        //post req start      
        $http({
            method: 'POST',
            url: $scope.url,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            transformRequest: function(obj) {
                var str = [];
                for (var p in obj)
                    str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                return str.join("&");
            },
            data: eddata
        }).success(function(response) {
            console.log(response);
            // alert('Edited successfully');
            window.top.postMessage('success^Edited Successfully', '*')
            // notie.alert({ type: 'success', text: 'Edited Successfully', stay: false })
            window.location.reload();
        }).catch(function onError(response) {
            // alert('Some error occured');
            window.top.postMessage('error^Some Error Occured', '*')
            // notie.alert({ type: 'error', text: 'Some error occured', stay: false })
            window.location.reload();
        });
        //post req ends
    }




    var emodalPopup = function() {
        document.getElementById('mainsection').classList.add("blurcontent");
        return $scope.modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'blocks/modal/edit.html',
            scope: $scope
        });
    };
    $scope.editproduct = function(dta) {
        console.log('orignal', dta);
        $scope.edtid = dta.id;
        // for (key in dta){
        //     console.log(key);
        // dta[key] = $scope.humanize(dta[key])
        //     // Object.defineProperty(dta, $scope.humanize(key),
        //     //     Object.getOwnPropertyDescriptor(dta, key));
        //     // delete dta[key];
        // }


        $scope.edls = dta;
        console.log('scope', $scope.edls);

        emodalPopup().result
            .then(function(data) {

            })
            .then(null, function(reason) {
                document.getElementById('mainsection').classList.remove("blurcontent");

            });
    }




    var dmodalPopup = function() {
        document.getElementById('mainsection').classList.add("blurcontent");

        return $scope.modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'blocks/modal/ddialog.html',
            scope: $scope
        });
    };

    $scope.deletectg = function(id) {
        $scope.delcid = id;
        console.log(id);
        dmodalPopup().result
            .then(function(data) {

            })
            .then(null, function(reason) {
                document.getElementById('mainsection').classList.remove("blurcontent");

            });
    }

    $scope.deleteproduct = function(id) {
        $scope.delid = id;
        console.log(id);
        dmodalPopup().result
            .then(function(data) {

            })
            .then(null, function(reason) {
                document.getElementById('mainsection').classList.remove("blurcontent");

            });
    }

    $scope.delyes = function(id, vx) {

        var url = $scope.url + '?id=' + id + '&' + vx + '=true';
        // //post req start      
        $http({
            method: 'GET',
            url: url
        }).success(function(response) {
            console.log(response);
            window.top.postMessage('success^Deleted Successfully', '*')
            // notie.alert({ type: 'success', text: 'Deleted Successfully', stay: false })
            // alert("Deleted Successfully")
            window.location.reload();
        });
        // //post req ends
    }

    $scope.logout = function() {
        localStorage.removeItem("apikey");
        window.location.href = "index.html";
    }

    $scope.no = function() {
        $scope.modalInstance.dismiss('No Button Clicked')
    };

    $scope.grims = function() {
        var apife = localStorage.getItem("apikey")
        var syr = 'http://appsthink.com:1111/getimg/' + apife;
        $http({
            method: 'GET',
            url: syr
        }).success(function(response) {
            console.log(response);
            response.forEach(function(a) {
                a.filename = 'http://appsthink.com:1111/images/' + apife + '/' + a.filename;
            });
            $scope.imgr = response;
        });
    }


})