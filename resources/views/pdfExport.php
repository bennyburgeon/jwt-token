<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Example 1</title>
    <link rel="stylesheet" href="style.css" media="all" />
	<style>
		.clearfix:after {
  content: "";
  display: table;
  clear: both;
}

a {
  color: #5D6975;
  text-decoration: underline;
}

body {
  position: relative;
  width: 90%;  
  height: 100%; 
  margin: 0 auto; 
  color: #001028;
  background: #FFFFFF; 
  font-family: Arial, sans-serif; 
  font-size: 12px; 
  font-family: Arial;
}

header {
  padding: 10px 0;
  margin-bottom: 30px;
}

#logo {
  text-align: center;
  margin-bottom: 10px;
}

#logo img {
  width: 90px;
}

h1 {
  border-top: 1px solid  #5D6975;
  border-bottom: 1px solid  #5D6975;
  color: #5D6975;
  font-size: 2.4em;
  line-height: 1.4em;
  font-weight: normal;
  text-align: center;
  margin: 0 0 20px 0;
  background: url(dimension.png);
}

#project {
  float: left;
}

#project span {
  color: #5D6975;
  text-align: right;
  width: 52px;
  margin-right: 10px;
  display: inline-block;
  font-size: 0.8em;
}

#company {
  float: right;
  text-align: right;
}

#project div,
#company div {
  white-space: nowrap;        
}

table {
  width: 100%;
  border-collapse: collapse;
  border-spacing: 0;
  margin-bottom: 20px;
}

table tr:nth-child(2n-1) td {
  background: #F5F5F5;
}

table th,
table td {
  text-align: center;
}

table th {
  padding: 5px 20px;
  color: #5D6975;
  border-bottom: 1px solid #C1CED9;
  white-space: nowrap;        
  font-weight: normal;
}

table .service,
table .desc {
  text-align: left;
}

table td {
  padding: 20px;
  text-align: right;
}

table td.service,
table td.desc {
  vertical-align: top;
}

table td.unit,
table td.qty,
table td.total {
  font-size: 1.2em;
}

table td.grand {
  border-top: 1px solid #5D6975;;
}

#notices .notice {
  color: #5D6975;
  font-size: 1.2em;
}

footer {
  color: #5D6975;
  width: 100%;
  height: 30px;
  position: absolute;
  bottom: 0;
  border-top: 1px solid #C1CED9;
  padding: 8px 0;
  text-align: center;
}
	</style>
  </head>
  <body>
    <header class="clearfix">
      <div id="logo">
        <img src="logo.png">
      </div>
      <h1>INVOICE 3-2-1</h1>
     
      <div id="project">
        <div><span>Name</span> <?php echo $data->customers->customer_name; ?></div>
        <div><span>Contact</span> <?php echo $data->customers->contact_number; ?></div>
        <div><span>Email</span> <?php echo $data->customers->email; ?></div>
        <div><span>Address</span> <?php echo $data->customers->address; ?></div>
        <div><span>Purchase Date</span> <?php echo date("d-M-Y", strtotime($data->created_at)); ?></div>
      </div>
    </header>
    <main>
      <table>
        <thead>
          <tr>
            <th>PRODUCT</th>
            <th>PRODUCT CODE</th>
            <th>PRICE</th>
            <th>CGST</th>
			<th>SGST</th>
			<th>QTY</th>
            <th>TOTAL</th>
          </tr>
        </thead>
        <tbody>
			<?php foreach($data->details as $detail){ ?>
          <tr>
            <td><?php echo $detail->products->product_name;; ?></td>
            <td><?php echo $detail->products->product_code; ?></td>
            <td><?php echo $detail->price ;?></td>
            <td><?php echo $detail->cgst; ?>%</td>
            <td><?php echo $detail->sgst; ?>%</td>
			<td><?php echo $detail->quantity; ?></td>
			<td><?php echo $detail->total; ?></td>
          </tr>
          <?php } ?>
          <tr>
            <td colspan="4">SUBTOTAL</td>
            <td class="total">Rs.<?php echo $data->price; ?></td>
          </tr>
          <tr>
            <td colspan="4">Discount</td>
            <td class="total">Rs.<?php echo $data->price; ?></td>
          </tr>
          <tr>
            <td colspan="4" class="grand total">GRAND TOTAL</td>
            <td class="grand total">Rs.<?php echo $data->price; ?></td>
          </tr>
        </tbody>
      </table>
      <div id="notices">
        <div>NOTICE:</div>
        <div class="notice">A finance charge of 1.5% will be made on unpaid balances after 30 days.</div>
      </div>
    </main>
    <footer>
      Invoice was created on a computer and is valid without the signature and seal.
    </footer>
  </body>
</html>