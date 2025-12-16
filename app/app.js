/* global angular */

const app = angular.module("MTapp", ["ui.bootstrap"]);

app.filter("startFrom", function () {
  return function (input, start) {
    if (input) {
      start = Number(start); //parse to int
      return input.slice(start);
    }
    return [];
  };
});

app.filter("vla", function () {
  return function (str) {
    var i,
      frags = str.split("_");
    for (i = 0; i < frags.length; i++) {
      frags[i] = frags[i].charAt(0).toUpperCase() + frags[i].slice(1);
    }
    return frags.join(" ");
  };
});

app.controller("mtctrl", function ($scope, $http, $location, $uibModal, $q) {
  // console.log(localStorage.getItem("apikey"));
  $scope.url = window.location.protocol + '//' + window.location.hostname + '/' + window.location.pathname.split('/')[1] + '/api';
  var passphrase = "yug";

  $scope.humanize = function (str) {
    var i,
      frags = str.split("_");
    for (i = 0; i < frags.length; i++) {
      frags[i] = frags[i].charAt(0).toUpperCase() + frags[i].slice(1);
    }
    return frags.join(" ");
  };

  $scope.removefromArray = function (array, ...items) {
    return array.filter((item) => !items.includes(item));
  };

  $scope.removeFromObject = function (obj, ...properties) {
    // Create new object with all properties except those in properties array
    return Object.keys(obj)
      .filter((key) => !properties.includes(key))
      .reduce((newObj, key) => {
        newObj[key] = obj[key];
        return newObj;
      }, {});
  };

  $scope.setPage = function (pageNo) {
    $scope.currentPage = pageNo;
    // Allow DOM to update with new page content
    $scope.adjustCells();
  };

  $scope.filter = function () {
    $timeout(function () {
      $scope.filteredItems = $scope.list.length;
    }, 10);
  };

  $scope.sort_by = function (predicate) {
    $scope.predicate = predicate;
    $scope.reverse = !$scope.reverse;
  };

  // Watch for pagination changes
  $scope.$watch("currentPage", function (newPage) {
    if (newPage) {
      console.log("Current page:", newPage);
      $scope.adjustCells();
    }
  });

  $scope.adjustCells = function () {
    setTimeout(() => {
      console.log("adjustCells called");
      var cells = document.getElementsByTagName("td");
      Array.from(cells).forEach(function (cell) {
        var text = cell.textContent.trim();
        if (text.startsWith("https")) {
          if (text.match(/\.(jpg|jpeg|png|gif|webp)$/i)) {
            cell.innerHTML = `
                    <a href="${text}" target="_blank">
                        <img src="${text}" style="max-width: 100px; max-height: 100px;">
                    </a>`;
          } else {
            cell.innerHTML = `<a href="${text}" target="_blank">${text}</a>`;
          }
        }
      });
    }, 100);
  };

  $scope.trans = function (obj) {
    var str = [];
    for (var p in obj)
      str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
    return str.join("&");
  };

  $scope.login = function (user, pass) {
    var str = pass;

    var regex = new RegExp(passphrase, "g");
    var resx = str.match(regex);
    var pass = 0;
    if (resx) {
      if (resx.length > 0) {
        var pass = 1;
        window.location.href = "adm.html";
        console.log("found");
      } else {
        console.log("notfound");
      }
    } else {
      // notie.alert({ type: 'error', text: '', stay: false })
      window.top.postMessage("error^Wrong password", "*");
      // alert("Wrong password");
      window.location.href = "index.html";
    }
  };

  $scope.cancelbt = function () {
    $scope.modalInstance.dismiss("cancel");
    document.getElementById("mainsection").classList.remove("blurcontent");
  };

  $scope.flfl = function () {
    // console.log($scope.filtered);
    var pp = [];
    for (let i = 0; i < $scope.filtered.length; i++) {
      pp.push($scope.filtered[i].id);
    }
    // console.log(pp);
    $http.get($scope.url + "?deliid=true&iid=" + pp).success(function (data) {
      console.log(data);
    });
  };

  $scope.admin = function () {
    $scope.userdata = JSON.parse(localStorage.getItem("userdat"));
    $scope.userole = $scope.userdata.role;
    $http
      .get($scope.url + "?getcontent=true&role=" + $scope.userole)
      .success(function (data) {
        console.log(data);

        localStorage.setItem("getcontent", JSON.stringify(data));
        // $scope.humanize
        $scope.list = data;

        if (data.length > 0) {
          $scope.showdat = Object.keys($scope.list[0]);
          for (var i = 0; i < $scope.showdat.length; i++) {
            $scope.showdat[i] = $scope.humanize($scope.showdat[i]);
          }

          // delete $scope.list.created_at;
          $scope.fields = Object.keys($scope.list[0]);

          $http
            .get($scope.url + "?getfirstcontent=true&role=" + $scope.userole)
            .success(function (data) {
              // console.log(data);
              console.log("getfirstcontent got data in first attempt", data);
              $scope.addingNew = Object.values(data);
              $scope.addingNew = $scope.removefromArray(
                $scope.addingNew,
                "role",
                "created_at"
              );

              console.log("after removefromarray addingnew", $scope.addingNew);

              $scope.adjustCells();

              window.top.postMessage("responseact", "*");
            });

          for (var i = 0; i < $scope.fields.length; i++) {
            $scope.fields[i] = $scope.humanize($scope.fields[i]);
          }

          console.log("fields", $scope.fields);
          $scope.idun = data[0].id;
          $scope.currentPage = 1; //current page
          $scope.entryLimit = 5; //max no of items to display in a page
          $scope.filteredItems = $scope.list.length; //Initially for no filter S
        } else {
          console.log("nodata");

          $http
            .get($scope.url + "?getfirstcontent=true" + $scope.userdata.role)
            .success(function (data) {
              console.log(
                "getfirstcontent else data coz nodata 2nd attempt",
                data
              );
              $scope.addingNew = Object.values(data);
              $scope.addingNew = $scope.removefromArray(
                $scope.addingNew,
                "role",
                "created_at"
              );
              console.log("addingnew when no data found", $scope.addingNew);
              $scope.addproduct();
              window.top.postMessage("responseact", "*");
            });
        }
      });
  };
  var amodalPopup = function () {
    document.getElementById("mainsection").classList.add("blurcontent");

    return ($scope.modalInstance = $uibModal.open({
      animation: true,
      templateUrl: "blocks/modal/create.html",
      scope: $scope,
    }));
  };

  $scope.addproduct = function () {
    amodalPopup()
      .result.then(function (data) {})
      .then(null, function (reason) {
        document.getElementById("mainsection").classList.remove("blurcontent");
      });
  };

  $scope.crtpag = function (action) {
    var addata = {};
    // Get user data from localStorage

    // Get form input values
    $("#adtfrm")
      .find("input")
      .each(function () {
        addata[this.name] = $(this).val();
      });

    // Add user type from localStorage
    addata["role"] = $scope.userdata.role;
    addata[action] = true;

    console.log("addata", $scope.url+'---'+addata);
    var url = $scope.url;

    //post req start
    $http({
      method: "POST",
      url: url,
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      transformRequest: function (obj) {
        var str = [];
        for (var p in obj)
          str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
        return str.join("&");
      },
      data: addata,
    })
      .success(function (response) {
        console.log(response + $scope.url);
        window.top.postMessage("success^Created Successfully", "*");
        window.location.reload();
      })
      .catch(function onError(response) {
        window.top.postMessage("error^Some Error Occured", "*");
        window.location.reload();
      });
  };

  $scope.eee = function () {
    function JSON2CSV(objArray) {
      var array = typeof objArray != "object" ? JSON.parse(objArray) : objArray;
      var str = "";
      var line = "";

      if ($("#labels").is(":checked")) {
        var head = array[0];
        if ($("#quote").is(":checked")) {
          for (var index in array[0]) {
            var value = String(index);
            line += '"' + value.replace(/"/g, '""') + '",';
          }
        } else {
          for (var index in array[0]) {
            line += index + ",";
          }
        }

        line = line.slice(0, -1);
        str += line + "\r\n";
      }

      for (var i = 0; i < array.length; i++) {
        var line = "";

        if ($("#quote").is(":checked")) {
          for (var index in array[i]) {
            var value = String(array[i][index]);
            line += '"' + value.replace(/"/g, '""') + '",';
          }
        } else {
          for (var index in array[i]) {
            line += array[i][index] + ",";
          }
        }

        line = line.slice(0, -1);
        str += line + "\r\n";
      }
      return str;
    }

    var json_pre = localStorage.getItem("csvs");

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
  };

  $scope.edityes = function (vx, id) {
    var eddata = {};
    $("#edtfrm")
      .find("input")
      .each(function () {
        if ($(this).val()) {
          var tyt = this.name;
          var uone = tyt.toLowerCase();
          var utwo = uone.replace(/ /g, "_");
          eddata[utwo] = $(this).val();
        } else {
          var posx = this.name;
          var oswnm = posx.toLowerCase();
          var ytt = oswnm.replace(/ /g, "_");
          eddata[ytt] = this.placeholder;
        }
      });

    eddata["id"] = $scope.edtid;
    eddata["role"] = $scope.userdata.role;
    eddata[vx] = true;
    console.log("eddata", $scope.url + "---" + JSON.stringify(eddata));

    //post req start
    $http({
      method: "POST",
      url: $scope.url,
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      transformRequest: function (obj) {
        var str = [];
        for (var p in obj)
          str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
        return str.join("&");
      },
      data: eddata,
    })
      .success(function (response) {
        console.log(response);
        // alert('Edited successfully');
        window.top.postMessage("success^Edited Successfully", "*");
        // notie.alert({ type: 'success', text: 'Edited Successfully', stay: false })
        window.location.reload();
      })
      .catch(function onError(response) {
        // alert('Some error occured');
        window.top.postMessage("error^Some Error Occured", "*");
        // notie.alert({ type: 'error', text: 'Some error occured', stay: false })
        window.location.reload();
      });
    //post req ends
  };

  var emodalPopup = function () {
    document.getElementById("mainsection").classList.add("blurcontent");
    return ($scope.modalInstance = $uibModal.open({
      animation: true,
      templateUrl: "blocks/modal/edit.html",
      scope: $scope,
    }));
  };
  $scope.editproduct = function (dta) {
    console.log("original edit data", dta);
    $scope.edls = $scope.removeFromObject(dta, "role", "created_at");
    $scope.edtid = dta.id;

    // Open modal first
    var modalPromise = emodalPopup();

    // Wait for modal to be fully rendered
    modalPromise.rendered.then(function () {
      $("#edtfrm")
        .find("input")
        .each(function () {
          var fieldName = $(this).attr("name").toLowerCase().replace(/ /g, "_");
          $(this).val(dta[fieldName]);
        });
    });

    modalPromise.result
      .then(function (data) {})
      .then(null, function (reason) {
        document.getElementById("mainsection").classList.remove("blurcontent");
      });
  };

  var dmodalPopup = function () {
    document.getElementById("mainsection").classList.add("blurcontent");

    return ($scope.modalInstance = $uibModal.open({
      animation: true,
      templateUrl: "blocks/modal/ddialog.html",
      scope: $scope,
    }));
  };

  $scope.deletectg = function (id) {
    $scope.delcid = id;
    console.log(id);
    dmodalPopup()
      .result.then(function (data) {})
      .then(null, function (reason) {
        document.getElementById("mainsection").classList.remove("blurcontent");
      });
  };

  $scope.deleteproduct = function (id) {
    $scope.delid = id;
    console.log(id);
    dmodalPopup()
      .result.then(function (data) {})
      .then(null, function (reason) {
        document.getElementById("mainsection").classList.remove("blurcontent");
      });
  };

  $scope.delyes = function (id, vx) {
    var url = $scope.url + "?id=" + id + "&" + vx + "=true";
    // //post req start
    $http({
      method: "GET",
      url: url,
    }).success(function (response) {
      console.log(response);
      window.top.postMessage("success^Deleted Successfully", "*");
      // notie.alert({ type: 'success', text: 'Deleted Successfully', stay: false })
      // alert("Deleted Successfully")
      window.location.reload();
    });
    // //post req ends
  };

  $scope.logout = function () {
    localStorage.removeItem("apikey");
    window.location.href = "index.html";
  };

  $scope.no = function () {
    $scope.modalInstance.dismiss("No Button Clicked");
  };

  $scope.grims = function () {
    var apife = localStorage.getItem("apikey");
    var syr = "http://appsthink.com:1111/getimg/" + apife;
    $http({
      method: "GET",
      url: syr,
    }).success(function (response) {
      console.log(response);
      response.forEach(function (a) {
        a.filename =
          "http://appsthink.com:1111/images/" + apife + "/" + a.filename;
      });
      $scope.imgr = response;
    });
  };

  $scope.allowplugin = function (fieldName) {
    localStorage.setItem("activeField", fieldName);

    // First remove any existing plugin elements
    const existingOverlay = document.getElementById("plugin-overlay");
    const existingSidebar = document.getElementById("plugin-sidebar");
    if (existingOverlay) document.body.removeChild(existingOverlay);
    if (existingSidebar) document.body.removeChild(existingSidebar);

    const sidebarHTML = `
        <div id="plugin-overlay" style="position: fixed; top: 0; left: 0; width: 20%; height: 100%; background: rgba(0,0,0,0.3); z-index: 2040; cursor: pointer; opacity: 0; transition: opacity 0.3s ease;"></div>
        <div id="plugin-sidebar" style="position: fixed; top: 0; right: -80%; width: 80%; height: 100%; background: white; z-index: 2050; transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: -2px 0 5px rgba(0,0,0,0.2);">
            <div style="padding: 15px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0;">Select Plugin</h3>
                <button id="close-plugin" style="border: none; background: none; font-size: 20px; cursor: pointer;width: 40px; height: 40px; border-radius: 50%; background-color: red; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold;">X</button>
            </div>
            <iframe id="plugin-frame" src="/plugins/" style="width: 100%; height: calc(100% - 60px); border: none;"></iframe>
        </div>
    `;

    // Insert HTML and get references in one go
    document.body.insertAdjacentHTML("beforeend", sidebarHTML);

    const sidebar = document.getElementById("plugin-sidebar");
    const overlay = document.getElementById("plugin-overlay");
    const closeBtn = document.getElementById("close-plugin");

    // Initialize elements and add listeners only after confirming they exist
    if (sidebar && overlay && closeBtn) {
      requestAnimationFrame(() => {
        overlay.style.opacity = "1";
        sidebar.style.transform = "translateX(-100%)";
      });

      const messageHandler = function (event) {
        if (typeof event.data === "string" && isNaN(event.data)) {
          const activeField = localStorage.getItem("activeField");
          const inputField = document.querySelector(
            `#edtfrm input[name="${activeField}"], #adtfrm input[name="${activeField}"]`
          );
          if (inputField) {
            inputField.value = event.data;
            $scope.$apply();
          }
          closeSidebar();
        }
      };

      const closeSidebar = () => {
        overlay.style.opacity = "0";
        sidebar.style.transform = "translateX(0)";
        setTimeout(() => {
          document.body.removeChild(sidebar);
          document.body.removeChild(overlay);
          window.removeEventListener("message", messageHandler);
        }, 300);
      };

      overlay.addEventListener("click", closeSidebar);
      closeBtn.addEventListener("click", closeSidebar);
      window.addEventListener("message", messageHandler);
    }
  };
});
