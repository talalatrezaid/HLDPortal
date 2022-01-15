<?php

use App\Models\ProductImage;
use Illuminate\Support\Facades\Http;


function getRolename($role_id)
{
  $role_name = DB::table('user_roles')->where('id', $role_id)->pluck('user_role')->first();
  return $role_name;
}
function Adminurl($url = NULL)
{
  if (env('ADMIN_PREFIX') <> "")
    return URL::to('/') . '/' . env('ADMIN_PREFIX') . '/' . $url;
  else
    return URL::to('/') . '/' . $url;
}
function CheckuserPermissions($action, $id = '')
{
  //true == no permission
  //false = user has permission
  //$id = id to be edited or deleted
  //add user -----only superadmin can add user
  if ($action == 'register') {
    if (Auth::user()->role_id <> 1 && Auth::user()->role_id <> 2)
      return true;
    else
      return false;
  }
  //edit user
  if ($action == 'edit') {
    //super admin can edit all users
    if (Auth::user()->role_id == 1) {
      return false;
    }
    //admin can only edit itself or lower user but can't edit super admin
    if (Auth::user()->role_id == 2) {
      if ($id  == 1)
        return true;
      else
        return false;
    }
    $role_id = DB::table('rezaid_users')->where('id', $id)->pluck('role_id')->first();
    //editor can only edit itself
    if (Auth::user()->role_id == 3) {
      if (Auth::user()->role_id == $role_id)
        return false;
      else
        return true;
    }
    //author can only edit itself
    if (Auth::user()->role_id == 4) {
      if (Auth::user()->role_id == $role_id)
        return false;
      else
        return true;
    }
  }
  //delete user
  if ($action == 'delete') {
    $role_id = DB::table('rezaid_users')->where('id', $id)->pluck('role_id')->first();
    //super admin can delete all users but can't delete itself
    if (Auth::user()->role_id == 1) {
      if (Auth::user()->role_id == $role_id)
        return true;
      else
        return false;
    }
    //admin can only delete lower user but can't delete itself or super admin
    if (Auth::user()->role_id == 2) {
      if (Auth::user()->role_id == $role_id)
        return true;
      elseif ($role_id == 1)
        return true;
      else
        return false;
    }
    //editor can not delete
    if (Auth::user()->role_id == 3)
      return true;
    //author can not delete
    if (Auth::user()->role_id == 4)
      return true;
  }
}
function shortCode($short_code)
{
  $form = DB::table('forms')->where('short_code', $short_code)->select('form_data', 'id')->first();
  if ($form == NULL) {
    echo '<strong style="color:red;">Invalid shortcode!!</strong>';
    return false;
  }
  $form_data = str_replace('disabled=""', '', $form->form_data);
  $form_data = str_replace('disabled="disabled"', '', $form_data);
  $form_data = str_replace('FORM_ID', $form->id, $form_data);
  echo $form_data;
}

/**
 * Method use to re-structure shopify pagination url string
 * Process the string and fetch formatted links
 * @param string
 * @return array
 */
function parsePaginationLinkHeader($headerLink)
{

  $available_links = [];
  $links = explode(',', $headerLink);
  foreach ($links as $link) {

    if (preg_match('/<(.*)>;\srel=\\"(.*)\\"/', $link, $matches)) {

      $query_str = parse_url($matches[1], PHP_URL_QUERY);
      parse_str($query_str, $query_params);
      $available_links[$matches[2]] = $query_params['page_info'];
    }
  }

  return $available_links;
}

/**
 * Call Magento API authentication endpoint to get authentication token.
 * Endpoint: https://domain.com/rest/V1/integration/admin/token
 * Method: POST
 * @param string, string
 * @return string
 */
function getMagentoAuthToken($base_url, $username, $password)
{

  // Magento authentication endpoint
  $magento_auth_api_endpoint  = 'V1/integration/admin/token';

  //http://ec2-3-8-20-90.eu-west-2.compute.amazonaws.com/rest/V1/integration/admin/token

  $response = Http::accept('application/json')->post($base_url . $magento_auth_api_endpoint, [
    'username' => $username,
    'password' => $password,
  ]);

  if (!$response->successful()) {

    $error_message = $response['message'];
    $result = "authentication_failed";
  }

  $result = $token = json_decode($response->body());

  return $result;
}


function write_product_image($product_id, $variantId)
{
  //check if i can write queury here
  $picture = ProductImage::select("source")->where("product_id", $product_id)->first();
  if (isset($picture['source']))
    return $picture['source'];
  else
    return "no image";
}

function write_order_donations($order_id)
{
  //writing self query because i need it in codeigniter helper also
  $donations = DB::select("select * from charity_donation inner join appeals on project_id = appeals.id where charity_donation.order_id =" . $order_id);

  foreach ($donations as $row) {
    $title = $row->project_name;
    $amount = $row->amount;
    $charity_type = $row->charity_type;
    $donation_type = "General Charity";
    if ($charity_type == 2) {
      $donation_type = "Sadqah";
    }

    if ($charity_type == 3) {
      $donation_type = "Zakat";
    }
    $bas_url = "http://92.205.25.25/~rezaid/charities/uploads/appeals/featured_image/";
    $featured_image = $bas_url . $row->featured_image;

?>

    <div class="row  my-4">
      <div class="col-12 text-left">
        <span><img class="img-fluid" src="<?php echo $featured_image; ?>" alt="" style="width: 60px; height:auto; border-radius: 10px" /></span>
        <span class="ml-4"><?php echo $title; ?> (£<?php echo $amount; ?>)
        </span>
        <br />
        <span class="badge badge-warning float-right"><?php echo $donation_type; ?></span>
      </div>
    </div>
  <?php
  }

  function write_charity_detail($charity)
  {
  ?>
    <div class="col-12 text-left"><?php echo $charity->charity_name; ?></div>
    <div class="col-12 text-left"><?php echo $charity->email; ?></div>
    <div class="col-12 text-left"><?php echo $charity->total_orders; ?> Orders</div>
  <?php
  }

  function write_customer_information($order_id)
  {
    $qry = DB::select("select customer.* from orders inner join customer on orders.customer_id = customer.id where orders.id =" . $order_id);
    $customer_information = null;
    foreach ($qry as $row) {
      $customer_information = $row;
      break;
    }
  ?>
    <div class="col-12 text-left"><?php echo $customer_information->first_name; ?> <?php echo $customer_information->last_name; ?></div>
    <div class="col-12 text-left"><?php echo $customer_information->email; ?></div>
    <div class="col-12 text-left"><?php echo $customer_information->phone; ?></div>
    <?php
  }


  function write_customer_billing_address($order_id)
  {
    $qry = DB::select("select * from order_billing_address where order_id = " . $order_id);
    $billing_information = null;
    foreach ($qry as $row) {
      $billing_information = $row;
      break;
    }
    if ($billing_information <> null) {
    ?>
      <div class="col-12 text-left"><?php echo $billing_information->address1; ?></div>
      <div class="col-12 text-left"><?php echo $billing_information->city; ?></div>
      <div class="col-12 text-left"><?php echo $billing_information->province; ?></div>
      <div class="col-12 text-left"><?php echo $billing_information->zip; ?></div>
      <div class="col-12 text-left"><?php echo $billing_information->address2; ?></div>
      <div class="col-12 text-left"><?php echo $billing_information->company; ?></div>
      <div class="col-12 text-left"><?php echo $billing_information->country; ?></div>
      <div class="col-12 text-left"><?php echo $billing_information->country_code; ?></div>


    <?php
    } else {
      echo "Not Available";
    }
  }

  function write_customer_shipping_address($order_id)
  {
    $qry = DB::select("select * from order_shipping_address where order_id = " . $order_id);
    $shipping_information = null;
    foreach ($qry as $row) {
      $shipping_information = $row;
      break;
    }
    ?>
    <div class="col-12 text-left"><?php echo $shipping_information->address1; ?></div>
    <div class="col-12 text-left"><?php echo $shipping_information->city; ?></div>
    <div class="col-12 text-left"><?php echo $shipping_information->province; ?></div>
    <div class="col-12 text-left"><?php echo $shipping_information->zip; ?></div>
    <div class="col-12 text-left"><?php echo $shipping_information->address2; ?></div>
    <div class="col-12 text-left"><?php echo $shipping_information->company; ?></div>
    <div class="col-12 text-left"><?php echo $shipping_information->country; ?></div>
    <div class="col-12 text-left"><?php echo $shipping_information->country_code; ?></div>


  <?php
  }

  function write_order_payment_infromation($order_id)
  {
    $qry = DB::select("select * from order_shipping_address where order_id = " . $order_id);
    $shipping_information = null;
    foreach ($qry as $row) {
      $shipping_information = $row;
      break;
    }
  ?>



<?php
  }
}
