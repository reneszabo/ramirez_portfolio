Parse.initialize("UvCgmylqiCIDqa3kB3WtNTMVaNmlxrE2rqkle7b4", "kFDnsbdc1lcjHy8WygftAcu7FR9JVifdNd8rQ7YL");
var Product = Parse.Object.extend("Product", {
  getName: function () {
    return this.get("name");
  },
  getPrice: function () {
    return this.get('price');
  },
  getDescription: function () {
    return this.get('description');
  },
  getImage: function () {
    var imageUrl = "/code/javascript-parse/no_image_thumb.gif";
    if (this.get('image') !== undefined) {
      imageUrl = this.get('image').url();
    }
    return imageUrl;
  },
  getSoldQuatity: function () {
    var x;
    var totalSold = 0;
    var logs = this.logs;
    for (x in logs) {
      var log = logs[x];
      totalSold += log.getQuantity();
    }
    return totalSold;
  }
  ,
  getStockQuantity: function () {
    var want = this.getLogTemporal().getQuantity();
    return this.get("quantity") * 1 - this.getSoldQuatity() * 1 - want * 1;
  },
  hasStock: function () {
    return this.getStockQuantity() > 0 ? true : false;
  },
  getLogTemporal: function () {
    if (this.tempLog === null) {
      this.tempLog = Log.new(this, 0);
    }
    return this.tempLog;
  },
  checkAvailability: function () {
    var finalCount = this.getStockQuantity();
    finalCount--;
    if (finalCount < 0) {
      //ALERT THE USER THAT WE DONT HAVE MORE PRODUCT
      alert('Out of stock');
    } else {
      this.getLogTemporal().setQuantity(this.getLogTemporal().getQuantity() + 1);
    }
  },
  initialize: function (attrs, options) {
    this.tempLog = null;
    this.logs = [];
  },
  setLogs: function (logs) {
    this.logs = logs;
  },
  addLog: function (log) {
    this.relation('logs').add(log);
  }
}, {
  // Class methods
  new : function (name, description, quantity, price, imageFile) {
    var product = new Product();
    product.set("name", name);
    product.set("description", description);
    product.set("price", price);
    product.set("image", imageFile);
    product.set("quantity", quantity);
    return product;
  }
});

var Log = Parse.Object.extend("Log", {
  getQuantity: function () {
    return this.get('quantity');
  },
  setQuantity: function (q) {
    this.set('quantity', q * 1);
  },
  initialize: function (attrs, options) {
  },
  getProduct: function () {
    return this.get('product');
  }
}, {
  new : function (product, quantity) {
    var l = new Log();
    l.set('product', product);
    l.set('quantity', quantity * 1);
    return l;
  }
});

var Order = Parse.Object.extend("Order", {
  initialize: function (attrs, options) {
    this.logs = null;
  },
  addLog: function (log) {
    this.relation('logs').add(log);
  },
  removeLog: function (log) {
    this.relation('logs').remove(log);
  },
  setLogs: function (logs) {
    this.logs = logs;
  },
  getLogs: function (logs) {
    return this.logs;
  },
  getCreatedAt: function () {
    return this.createdAt;
  }
}, {
  new : function (log) {
    var o = new Order();
    o.relation('logs').add(log);
    return o;
  }
})

