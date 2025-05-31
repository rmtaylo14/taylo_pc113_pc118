<?php 
include 'sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Food Ordering</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .food-card{border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,.1);padding:16px;margin:12px;text-align:center;background:#fff}
    .food-image{width:100%;height:150px;object-fit:cover;border-radius:12px}
    .checkout-list{max-height:300px;overflow-y:auto}
  </style>
</head>

<body class="bg-light">
  <div class="container py-5">
    <h1 class="text-center mb-4">Choose Your <span class="text-success">Food</span></h1>

    <div class="row" id="food-list"></div>

    <h3 class="mt-5">Checkout List</h3>
    <table class="table table-bordered">
      <thead>
        <tr><th>ID</th><th>Name</th><th>Description</th><th>Price</th><th>Image</th><th>Actions</th></tr>
      </thead>
      <tbody id="checkout-list"></tbody>
    </table>

    <div class="d-flex justify-content-between align-items-center mb-3">
      <strong>Total: ₱<span id="total">0.00</span></strong>
      <button id="place-order-btn" class="btn btn-primary" disabled>Place Order</button>
    </div>
  </div>

<script>
const checkout = [];

document.addEventListener("DOMContentLoaded", () => {
  const token = localStorage.getItem("token");
  if (!token) { window.location.href = "login.php"; return; }

  fetchMenuItems(token);

  document.getElementById('place-order-btn')
          .addEventListener('click', () => goToOrderSummary());
});

function fetchMenuItems(token){
  fetch("http://127.0.0.1:8000/api/index/menu",{headers:{Authorization:`Bearer ${token}`}})
      .then(r=>r.json()).then(renderFoodItems)
      .catch(()=>alert("Failed to load food items."));
}

function renderFoodItems(data){
  const c = document.getElementById('food-list'); c.innerHTML='';
  data.forEach(food=>{
    console.log(food);
    const col=document.createElement('div'); col.className='col-md-3';
    col.innerHTML=`
      <div class="food-card">
        <img src="http://127.0.0.1:8000/storage/${food.image_path}" class="food-image" alt="${food.name}">
        <h5 class="mt-2">${food.name}</h5>
        <p class="text-muted">₱${(+food.price).toFixed(2)}</p>
        <button class="btn btn-success" onclick='addToCart(${JSON.stringify(food)})'>Add To Cart</button>
      </div>`;
    c.appendChild(col);
  });
}

function addToCart(f){ checkout.push(f); updateCheckout(); }
function removeItem(i){ checkout.splice(i,1); updateCheckout(); }

function updateCheckout(){
  const list=document.getElementById('checkout-list'),
        totalEl=document.getElementById('total'),
        btn=document.getElementById('place-order-btn');
  list.innerHTML=''; let total=0;
  checkout.forEach((it,i)=>{
    total+=+it.price;
    list.insertAdjacentHTML('beforeend',`
      <tr>
        <td>${it.id}</td><td>${it.name}</td><td>${it.description}</td>
        <td>₱${(+it.price).toFixed(2)}</td>
        <td><img src="http://127.0.0.1:8000/storage/${it.image_path}" width="50"></td>
        <td><button class="btn btn-sm btn-danger" onclick="removeItem(${i})">Remove</button></td>
      </tr>`);
  });
  totalEl.textContent=total.toFixed(2);
  btn.disabled=!checkout.length;
}

function goToOrderSummary(){
  localStorage.setItem('pendingOrder', JSON.stringify(checkout));
  window.location.href = 'orderuser.php';
}
</script>
</body>
</html>
