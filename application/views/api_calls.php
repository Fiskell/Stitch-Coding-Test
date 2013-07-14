<html>
<head>
</head>
<body>

  <div style="border: 1px solid #333">
    <span>Product GET</span>
    <ul>
      <li><a href="<?php echo Site_url();?>/request/products">/request/products</a> json response of all products</li>
      <li><a href="<?php echo Site_url();?>/request/products/147293252">/request/products/{product_id}</a> json response of a single product</li>
    </ul>
  </div>

  <div style="border: 1px solid #333">
    <span>Variant GET</span>
    <ul>
      <li><a href="<?php echo Site_url();?>/request/products/147293252/variant">/request/products/{product_id}/variant</a> json response of all variants for a product</li>
      <li><a href="<?php echo Site_url();?>/request/products/147293252/variant/336326064">/request/products/{product_id}/variant/{variant_id}</a> json response of a single product</li>
    </ul>
  </div>

  <div style="border: 1px solid #333">
    <span>Image GET Not gonna happen</span>
  </div>


  <div style="border: 1px solid #333">
    <span>Combos GET</span>
    <ul>
      <li><a href="<?php echo Site_url();?>/request/combos/147293252">/request/combos/{product_id}</a> json response of all combos</li>
    </ul>
  </div>

  <div style="border: 1px solid #333">
    <span>POST Update Stock BOOOOOO</span>
  </div>

  <div style="border: 1px solid #333">
    <span>POST Shopify Pull data to DB</span>
    <ul>
      <li><a href="<?php echo Site_url();?>/request/sync">/request/sync</a> Pull Shopify to database</li>
    </ul>
  </div>

  <div style="border: 1px solid #333">
    <span>POST add a combo (not started)</span>
  </div>

  <div style="border: 1px solid #333">
    <span>POST Push DB to Shopify (not started)</span>
  </div>

</body>
</html>
