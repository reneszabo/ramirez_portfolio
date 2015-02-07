

function Controller() {
  var user = null;
  var order = null;
  var orders = null;
  var products = null;
  this.init = function () {
    orders = new Orders();
    products = [
      new Product('Man Sun Glasses', 'Best Sun glasses a man can have', 'http://www.esquire.com/cm/esquire/images/D6/esq-burberry-sunglasses-0312-mdn.jpg', Math.ceil(Math.random() * 10), 20, this),
      new Product('Girl Sun Glasses', 'Best Sun glasses a girl can have', 'http://www.eyeglassworld.com/images/glasses/RAY-BAN-RB-4101-black-right.jpg', Math.ceil(Math.random() * 10), 21, this),
      new Product('Aviator Sun Glasses', 'No mater the gender, this glasses alwas look good', 'http://step-en.com/wp-content/uploads/2014/05/Travel-With-Aviator-Sunglasses.jpg', Math.ceil(Math.random() * 10), 25, this)
    ];
    newLayout();
  };
  this.buyNow = function () {
    orders.addOrder(order);
    for (var i in products) {
      if (products.hasOwnProperty(i)) {
        var product = products[i];
        var log = product.getLogTemporal();
        if (log.getQuantity() > 0) {
          log.setOrder(order); // MOST GO FIRST;
          order.addSoldLog(log);
          product.addSoldLog(log);
        }
      }
    }
    $('.cart-container').html('');
    renderOrders();
    newLayout();
  };
  function newLayout() {
    user = new User();
    order = new Order();
    order.setUser(user);
    user.setOrder(order);
    renderUser();
    for (var i in products) {
      if (products.hasOwnProperty(i)) {
        renderProduct(products[i], true);
      }
    }
  }
  function renderProduct(product, reset) {
    if (reset === undefined) {
      reset = false;
    }
    var $productContainer = $('#' + product.getId());
    if ($productContainer.length === 0) {
      $productContainer = $('<div id="' + product.getId() + '" class="col-md-4"></div>');
      var $myPanel = $('<div class="panel panel-default"><div class="panel-heading title">Demo Name</div><div class="panel-body"><img src="" class="img-responsive" /><div class="description">Demo Description</div><div class="text-right"><strong>$<span class="price"></span></strong></div></div><div class="panel-footer"><div class="text-right"><ul class="list-inline" style="margin-bottom:0;"><li><span class="stock-number small">8 in stock demo</span></li><li style="padding-right:0;"><button class="add-to-cart btn btn-info disabled"><i class="fa fa-shopping-cart"></i></button></li></ul></div> </div></div>');
      $productContainer.appendTo($('.products-container'));
      $myPanel.appendTo($productContainer);
      $productContainer.find('.add-to-cart').on('click', function (e) {
        product.checkAvailability(e);
        renderProduct(product);
        renderShoppingCart(product);
      });
      $productContainer.find('.title').html('<strong>' + product.getName() + '</strong>');
      $productContainer.find('img').attr('src', product.getImage());
      $productContainer.find('.description').html(product.getDescription());
      $productContainer.find('.price').html(product.getPrice());
    }
    if (reset) {
      $productContainer.data('quantity', 0);
      $productContainer.find('.add-to-cart').addClass('disabled');
      product.setLogTemporal(null);
    }
    if (product.hasStock()) {
      $productContainer.find('.stock-number').html(product.getStockQuantity() + ' in stock ');
    } else {
      $productContainer.find('.stock-number').html(' OUT OF STOCK ');
    }
  }
  ;
  function renderUser() {
    var $myUserForm = $('<div class=" col-md-10"><input type="text" placeholder="Full name" class="form-control"/></div><button type="submit" class="btn btn-default">Submit</button>');
    $('.user-container').html('');
    $myUserForm.appendTo($('.user-container'));
    $('#buy-now').addClass('disabled');
    $('.add-to-cart').addClass('disabled');
    $('.user-container').closest('form').on('submit', handleUserForm);
  }
  ;
  function handleUserForm(e) {
    console.log('BOOM');
    e.preventDefault();
    handleSubmitUser();
  }
  function handleSubmitUser() {
    var userfullname = $('.user-container').find('input').val();
    user.setName(userfullname);
    $('.user-container').closest('form').off('submit', handleUserForm);
    $('.user-container').html('<div class=" col-md-12"><strong> Welcome </strong> ' + user.getName() + '<div>');
    $('.add-to-cart').removeClass('disabled');
    $('#buy-now').removeClass('disabled');
  }
  function renderOrders() {
    $('.summary-container').html('');
    var o = orders.getOrders();
    $.each(o, function (index, order) {
      var $r = $('<div class="well well-small"></div> ');
      var $label = ('<div>' + order.getUser().getName() + ' - ' + order.getDate() + '</div>');
      $r.append($label);
      var logs = order.getLogs();
      for (var i in logs) {
        if (logs.hasOwnProperty(i)) {
          var log = logs[i];
          var prod = log.getProduct();
          var name = prod.getName();
          var quantity = log.getQuantity();
          var $p = $('<div>' + name + ' - Quantity: ' + quantity + '</div>');
          $r.prepend($p);
        }
      }
      $('.summary-container').append($r);
    });
  }
  function renderShoppingCart(product) {
    var $myContainer = $('.cart-container').find('.' + product.getId());
    if ($myContainer.length === 0) {
      $myContainer = $('<tr class="' + product.getId() + '"><td class="name"></td><td class="quantity"></td></tr>');
      $myContainer.appendTo($('.cart-container'));
      $myContainer.find('.name').html(product.getName());
    }
    $myContainer.find('.quantity').html(product.getLogTemporal().getQuantity());
  }
}
var controller = new Controller();
controller.init();
$(document).on('click', '#buy-now', controller.buyNow);