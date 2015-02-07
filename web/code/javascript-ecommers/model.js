function Product(name, description, image, quantity, price) {
  var name = name;
  var description = description;
  var image = image;
  var quantity = quantity;
  var price = price;
  var logs = [];
  var tempLog = null;
  this.getId = function () {
    return  name.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
  };
  this.getName = function () {
    return name;
  };
  this.getImage = function () {
    return image;
  };
  this.getDescription = function () {
    return description;
  };
  this.getPrice = function () {
    return price;
  };
  this.getLogTemporal = function () {
    if (tempLog === null) {
      tempLog = new Log(this);
      tempLog.setQuantity(0);
    }
    return tempLog;
  };
  this.setLogTemporal = function (l) {
    tempLog = l;
  };
  this.addSoldLog = function (logObj) {
    var x;
    for (x in logs) {
      if (logs.hasOwnProperty(x) && logs[x] === logObj) {
        console.log('No the log is in the array allready');
        return false; // VALIDATE THAT THE OBJECT IS NOT ALL READY IN THE ARRAY
      }
    }
    if (logObj.getProduct() !== this) {
      console.log('No the log is not from this product');
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
    var want = this.getLogTemporal().getQuantity();
    return quantity * 1 - this.getSoldQuatity() * 1 - want * 1;
  };
  this.hasStock = function () {
    return this.getStockQuantity() > 0 ? true : false;
  };
  this.checkAvailability = function (e) {
    var finalCount = this.getStockQuantity();
    finalCount--;
    if (finalCount < 0) {
      //ALERT THE USER THAT WE DONT HAVE MORE PRODUCT
      alert('Out of stock');
    } else {
      this.getLogTemporal().setQuantity(this.getLogTemporal().getQuantity() + 1);
    }
  };
}



function Orders() {
  var orders = [];
  this.addOrder = function (orderObj) {
    var x;
    for (x in orders) {
      if (orders.hasOwnProperty(x) && orders[x] === orderObj) {
        return false; // VALIDATE THAT THE OBJECT IS NOT ALL READY IN THE ARRAY
      }
    }
    orders.push(orderObj);
    return true; // ALL GOOD XD
  };
  this.getOrders = function () {
    return orders;
  };
}
function Order() {
  var date = new Date();
  var user = null;
  var logs = [];
  this.addSoldLog = function (logObj) {
    var x;
    for (x in logs) {
      if (logs.hasOwnProperty(x) && logs[x] === logObj) {
        console.log('No the log is in the array allready');
        return false; // VALIDATE THAT THE OBJECT IS NOT ALL READY IN THE ARRAY
      }
    }
    if (logObj.getOrder() !== this) {
      console.log('No the order is not the same');
      return false; // VALIDATE THAT THE RELATIONS ARE THE SAME PRODUCT
    }
    logs.push(logObj);
    return true; // ALL GOOD XD
  };
  this.getDate = function () {
    var curr_date = date.getDate();
    var curr_month = date.getMonth();
    var curr_year = date.getFullYear();
    return (curr_date + "-" + curr_month + "-" + curr_year);
  };
  this.getUser = function () {
    return user;
  };
  this.setUser = function (u) {
    user = u;
  };
  this.getLogs = function () {
    return logs;
  };
}
function Log(productObj) {
  var product = productObj;
  var quantity = 0;
  var order = null;
  this.getProduct = function () {
    return product;
  };
  this.setQuantity = function (q) {
    quantity = q;
  };
  this.getQuantity = function () {
    return quantity;
  };
  this.getOrder = function () {
    return order;
  };
  this.setOrder = function (o) {
    order = o;
  };
}
function User() {
  var fullname = null;
  var order = null;
  this.getName = function () {
    return fullname;
  };
  this.setName = function (n) {
    fullname = n;
  };
  this.getOrder = function () {
    return order;
  };
  this.setOrder = function (o) {
    order = o;
  };
}
