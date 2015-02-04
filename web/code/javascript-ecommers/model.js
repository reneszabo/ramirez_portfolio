function Product(name, description, image, quantity) {
  var name = name;
  var description = description;
  var image = image;
  var quantity = quantity;
  var logs = [];
  this.addSoldLog = function (logObj) {
    var x;
    for (x in logs) {
      if (logs.hasOwnProperty(x) && logs[x] === logObj) {
        return false; // VALIDATE THAT THE OBJECT IS NOT ALL READY IN THE ARRAY
      }
    }
    if (logObj.product !== this) {
      return false; // VALIDATE THAT THE RELATIONS ARE THE SAME PRODUCT
    }
    logs.push(logObj);
    return true; // ALL GOOD XD
  };
  this.getSoldLogs = function () {
    return logs;
  };
  this.getSoldQuatity = function () {
    var x;
    var totalSold = 0;
    for (x in logs) {
      if (logs.hasOwnProperty(x)) {
        totalSold += logs[x].getQuantity();
      }
    }
    return totalSold;
  };
  this.getStockQuantity = function () {
    return quantity - this.getSoldQuatity();
  };
  this.hasStock = function () {
    return this.getStockQuantity() > 0 ? true : false;
  };
  this.renderProduct = function () {
    var nameId = name.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
    var $myContainer = $('#' + nameId);
    if ($myContainer.length === 0) {
      $myContainer = $('<div id="' + nameId + '" class="col-md-4"></div>');
      var $myPanel = $('<div class="panel panel-default"><div class="panel-heading title"></div><div class="panel-body"><img src="" class="img-responsive" /><div class="description"></div></div></div>')
      $myContainer.appendTo($('.products-container'));
      $myPanel.appendTo($myContainer);
    }
    $myContainer.find('.title').html(name);
    $myContainer.find('img').attr('src',image);
    $myContainer.find('.description').html(description);
  };
  this.checkAvailability = function (numberWantend) {
    var have = this.getStockQuantity();
    var finalCount = have - numberWantend;
    if (finalCount < 0) {
      //ALERT THE USER THAT WE DONT HAVE MORE PRODUCT
    } else {
      // ALL GOOD XD
    }
  };
  this.renderProduct();
}
function Orders() {
  this.orders = [];
  this.addOrder = function (orderObj) {

  };
  this.renderOrders = function () {

  };
}
function Order(date, userObj) {
  this.date = date;
  this.user = userObj;
  this.logs = [];
  this.addSoldLog = function (logObj) {

  };
  this.renderOrder = function () {

  };
}
function Log(productObj, quantity, orderObj) {
  var product = productObj;
  var quantity = quantity;
  var order = orderObj;

  this.getProduct = function () {
    return product;
  };
  this.getQuantity = function () {
    return quantity;
  };
  this.getOrder = function () {
    return order;
  };
}
function User(fullname) {
  this.fullname = fullname;
  this.order = null;
  this.setOrder = function (orderObj) {

  };
  this.renderPurchace = function () {

  };
}
