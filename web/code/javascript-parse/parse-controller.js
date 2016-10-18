/* GLOBAL VARIABLES */
var orders = null;
var products = null;
var totalShoppingCart = 0;
/*
 * Function found in http://jsfiddle.net/gabrieleromanato/QWQdz/
 * jQuery PlugIn to validates the extencion of a file
 */
(function ($) {
  $.fn.checkFileType = function (options) {
    var defaults = {
      allowedExtensions: [],
      success: function () {
      },
      error: function () {
      }
    };
    options = $.extend(defaults, options);
    return this.each(function () {
      $(this).on('change', function () {
        var value = $(this).val(),
                file = value.toLowerCase(),
                extension = file.substring(file.lastIndexOf('.') + 1);
        if ($.inArray(extension, options.allowedExtensions) == -1) {
          options.error();
          $(this).focus();
        } else {
          options.success();
        }
      });
    });
  };
})(jQuery);
/*
 * Listener that validates the file and if its not in the valide extencions 
 * it displays a error message in the DOM.
 */
$('#image').checkFileType({
  allowedExtensions: ['jpg', 'jpeg', 'gif', 'png'],
  success: function () {
    console.log("isImage - success");
  },
  error: function () {
    console.log("isImage - error");
    $('#image').replaceWith($('#image').clone(true));
    $('.image-error').fadeIn().delay(5000).fadeOut();
  }
});
/*
 * Function that clears the form out of characters in the inputs.
 */
function clearCreateForm() {
  $('#name').val('');
  $('#description').val('');
  $('#price').val('');
  $('#quantity').val('');
  $('#image').replaceWith($('#image').clone(true));
  $('#form-fields').removeAttr('disabled');
}
/*
 * Listener that handles the submit, prevents the submit event from the form
 * and calls the function CREATE PRODUCT that handles the CALL to send all 
 * the INFO to PARSE.
 */
$("form#form-create-product").on('submit', function (e) {
  e.preventDefault();
  $('.product-processing').fadeIn();
  var data = $(this).serializeArray();
  $('#form-fields').attr('disabled', '');
  console.log('SUBMIT create product', data);
  var fileUploadControl = $("#image")[0];
  if (fileUploadControl.files.length > 0) {
    var file = fileUploadControl.files[0];
    var name = "photo.jpg";
    var parseFile = new Parse.File(name, file, "image/jpg");
    parseFile.save().then(function () {
      console.log("saveFile JPG - success");
      createProduct(data[0].value, data[1].value, data[2].value * 1, data[3].value * 1, parseFile);
      $('.product-processing').hide();
      $('.product-success').fadeIn().delay(5000).fadeOut();
      clearCreateForm();
    }, function (error) {
      $('.product-processing').hide();
      $('.product-error').fadeIn().delay(5000).fadeOut();
      console.log("saveFile JPG - error", error);
    });
  }
});
/*
 * Functions that submits the info to PARSE to create the PRODUCT 
 * @param {string} name
 * @param {string} description
 * @param {int} quantity
 * @param {int} price
 * @param {File} imageFile
 */
function createProduct(name, description, quantity, price, imageFile) {
  var product = Product.new(name, description, quantity, price, imageFile);
  product.save().then(function (obj) {
    console.log("createProduct - success", obj);
    renderProducts();
  }, function (error) {
    console.log("createProduct - error", error);
  });
}
/*
 * Fetch last 6 products from PARSE or pass limit as a parameter
 * @param {int} limit
 * @returns {undefined}
 */
function renderProducts(limit) {
  if (limit === undefined) {
    limit = 6;
  }
  var query = new Parse.Query(Product);
  query.descending("createdAt");
  query.limit(limit);
  query.find().then(function (results) {
    console.log("renderProducts - success", results);
    products = results;
    var promise = Parse.Promise.as();
    $.each(products, function (index, product) {
      var logRelation = product.relation("logs");
      var logQuery = logRelation.query();
      promise = promise.then(function () {
        return logQuery.find({
          success: function (logResults) {
            product.setLogs(logResults);
          },
          error: function (error) {
          }
        });
      });
    });
    return promise;
  }, function (error) {
    console.log("renderProducts - error", error);
  }).then(function () {
    console.log("renderProducts LOGS- success");
    $.each(products, function (index, product) {
      renderProduct(product);
    });
  }, function (error) {
    console.log("renderProducts logs- error", error);

  });
}
renderProducts();

/*
 * Reuse the function from the past project 
 * 
 * @param {Product} product
 */
function renderProduct(product) {
  console.log("renderProduct -> " + product.id);
  var $productContainer = $('#' + product.id);
  if ($productContainer.length === 0) {
    $productContainer = $('<div id="' + product.id + '" class="col-md-4"></div>');
    var $myPanel = $('<div class="panel panel-default"><div class="panel-heading title">Demo Name</div><div class="panel-body"><div style="height: 150px; overflow: hidden;"><img src="" class="img-responsive" /></div><div class="description">Demo Description</div><div class="text-right"><strong>$<span class="price"></span></strong></div></div><div class="panel-footer"><div class="text-right"><ul class="list-inline" style="margin-bottom:0;"><li><span class="stock-number small">8 in stock demo</span></li><li style="padding-right:0;"><button class="add-to-cart btn btn-info"><i class="fa fa-shopping-cart"></i></button></li></ul></div> </div></div>');
    $productContainer.appendTo($('#products-container'));
    $myPanel.appendTo($productContainer);
    $productContainer.find('.title').html('<strong>' + product.getName() + '</strong>');
    $productContainer.find('.add-to-cart').on('click', function (e) {
      var p = null;
      for (var i in products) {
        p = products[i];
        if (p.id === product.id) {
          break;
        }
      }
      p.checkAvailability();
      renderProduct(p);
      renderShoppingCart(p);
    });
    $productContainer.find('img').attr('src', product.getImage());
    $productContainer.find('.description').html(product.getDescription());
    $productContainer.find('.price').html(product.getPrice());
  }
  if (product.hasStock()) {
    $productContainer.find('.stock-number').html(product.getStockQuantity() + ' in stock ');
  } else {
    $productContainer.find('.stock-number').html('out of stock');
  }
}
/*
 * Render the shoping cart
 */
function renderShoppingCart(product) {
  var $myContainer = $('.cart-container').find('.' + product.id);
  if ($myContainer.length === 0) {
    $myContainer = $('<tr class="' + product.id + '"><td class="name"></td><td class="quantity"></td><td class="price"></td><td class="iq">=</td><td class="total"></td></tr>');
    $myContainer.appendTo($('.cart-container'));
    $myContainer.find('.name').html(product.getName());
  }
  var quantity = product.getLogTemporal().getQuantity() * 1;
  var price = product.getPrice() * 1;
  $myContainer.find('.quantity').html(quantity);
  var totalPrice = quantity * price;
  $myContainer.find('.price').html("$ " + price);
  $myContainer.find('.total').html("$ " + totalPrice);
  renderShoppingCartTotal();
}
/*
 * Calculate the total for all products in the shopping cart
 */
function renderShoppingCartTotal() {
  totalShoppingCart = 0;
  for (var i in products) {
    if (products.hasOwnProperty(i)) {
      var product = products[i];
      var log = product.getLogTemporal();
      if (log.getQuantity() > 0) {
        var quantity = product.getLogTemporal().getQuantity() * 1;
        var price = product.getPrice() * 1;
        totalShoppingCart += quantity * price;
        $('#buy-now').removeClass('disabled');
      }
    }
  }
  var $foot = $('.cart-container').closest('table').find('tfoot');
  if ($foot.length === 0) {
    $foot = $('<tfoot></tfoot>');
    $foot.appendTo($('.cart-container').closest('table'));
  }
  $foot.html('<tr><td colspan="4" style="text-align: right;">Total</td><th> $ ' + totalShoppingCart + "</th></tr>");
}

/*
 * Handle BUY NOW Button and save it in PARSE
 */
function buyNow() {
  var logs = [];
  var logsSave = [];
  for (var i in products) {
    if (products.hasOwnProperty(i)) {
      var product = products[i];
      var log = product.getLogTemporal();
      if (log.getQuantity() > 0) {
        logs.push(log);
      }
    }
  }
  if (logs.length > 0) {
    for (var k in logs) {
      logsSave.push(logs[k].save());
    }

    Parse.Promise.when(logsSave).then(function () {
      var order = Order.new(logs);
      order.save().then(function () {
        var promise = Parse.Promise.as();
        $.each(logs, function (index, log) {
          var p = log.get('product');
          p.addLog(log);
          promise = promise.then(function () {
            return  p.save();
          });
        });
        return promise;
      }, function (error) {
        //ORDER HAS ERROR
        alert("ORDER DID NOT SAVE PROPERLY");
      }).then(function () {
        //PRODUCTS WHERE SAVE OK
        renderOrders();
        renderProducts();
        alert("WoW :) - Thx for buying !");
      }, function (error) {
        //PRODUCTS HAVE ERRORS
        alert("ORDER DID NOT SAVE PROPERLY !");
      });
    }, function (error) {
      //LOGS HAVE ERROR
      alert("ORDER DID NOT SAVE PROPERLY !!");
    });


  } else {
    alert('NO SALES MADE');
  }
  $('.cart-container').closest('table').find('tfoot').html('');
  $('.cart-container').html('');
  $('#buy-now').addClass('disabled');
}

/*
 * Render last 6 Orders fetched from PARSE
 */
function renderOrders(limit) {
  if (limit === undefined) {
    limit = 6;
  }
  orders = [];
  var query = new Parse.Query(Order);
  query.limit(limit);
  query.descending("createdAt");
  query.find().then(function (o) {
    console.log("renderOrder - success");
    var promise = Parse.Promise.as();
    orders = o;
    $.each(orders, function (index, order) {
      var logRelation = order.relation("logs");
      var logQuery = logRelation.query();
      logQuery.include("product");
      promise = promise.then(function () {
        return logQuery.find({
          success: function (logResults) {
            order.setLogs(logResults);
          },
          error: function (error) {
          }
        });
      });
    });
    return promise;
  }, function (error) {
    console.log("renderOrder - error", error);
  }).then(function () {
    console.log("renderOrder logs - success");
    $('.summary-container').html('');
    $.each(orders, function (index, order) {
      var $r = $('<div class="well well-small"></div> ');
      var $label = ('<div> <b>Order ID:</b> ' + order.id + ' - ' + order.getCreatedAt() + '</div>');
      $r.append($label);
      var logs = order.getLogs();
      for (var i in logs) {
        if (logs.hasOwnProperty(i)) {
          var log = logs[i];
          var prod = log.getProduct();
          var name = prod.getName();
          var quantity = log.getQuantity();
          var $p = $('<div>' + name + ' - Quantity: ' + quantity + '</div>');
          $r.append($p);
        }
      }
      $('.summary-container').append($r);
    });
  }, function (error) {
    console.log("renderOrder logs - error", error);

  });
}
renderOrders();

$(document).on('click', '#buy-now', buyNow);


