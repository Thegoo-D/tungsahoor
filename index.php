<?php
date_default_timezone_set("Asia/Jakarta");

/* 1. PAGE VIEW COUNTER */
$counterFile = "counter.txt";
$views = 0;

$fp = fopen($counterFile, "c+");
if ($fp) {
    flock($fp, LOCK_EX);
    $views = (int) trim(stream_get_contents($fp));
    $views = $views + 1;
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, (string) $views);
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
}

/* ---------- help function ---------- */
function e($text)
{
    return htmlspecialchars($text, ENT_QUOTES, "UTF-8");
}

function money($number)
{
    return "$" . number_format($number, 2);
}

/* 2. Product Data */
$products = [
    1 => [
        "name"  => "Fortnite Tung",
        "desc"  => "Have you heard Fortnite before? Tung is coming into it.",
        "price" => 67,
        "img"   => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRdw33vu9iFW5DABhWzI4R8E_XpVfrVWYbvjBVF16z5iSiuLnahbhuS49Zn&s=10",
    ],
    2 => [
        "name"  => "Flowers Tung",
        "desc"  => "For those who has girlfriend or boyfriend or crush.",
        "price" => 100,
        "img"   => "https://community.robotime.com/uploads/default/optimized/3X/d/a/dacab1bb41641288992943ca04256c5f382d7882_2_562x750.jpeg",
    ],
    3 => [
        "name"  => "Angklung Tung",
        "desc"  => "Local traditional instrument from Indonesia that sound amazing.",
        "price" => 50,
        "img"   => "https://thumb.wikimedia.org/wikipedia/commons/thumb/2/2f/Angklung_Baduy.jpg/1280px-Angklung_Baduy.jpg",
    ],
    4 => [
        "name"  => "Bamboo Tung",
        "desc"  => "Simple umberella safety place to save after rain.",
        "price" => 40,
        "img"   => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR8npbTSK_v_iMP8QwAAcoq1lFW_BUu4_O3_p6wdKmD956gkwIG10xsW9pQ&s=10",
    ],
    5 => [
        "name"  => "Tung Merch",
        "desc"  => "Mini tinny funny plushie. It soft and not wet.",
        "price" => 10,
        "img"   => "https://i.etsystatic.com/64795426/r/il/8ab50b/8176439501/il_570xN.8176439501_dac9.jpg",
    ],
    6 => [
        "name"  => "Tung Book",
        "desc"  => "Must read book in 2026. See how nature react.",
        "price" => 29,
        "img"   => "https://down-id.img.susercontent.com/file/id-11134207-81zti-me849rklqqyr90",
    ],
];

/* 3. Product Search (method GET) */
$keyword = isset($_GET["q"]) ? trim($_GET["q"]) : "";
$results = [];

foreach ($products as $id => $p) {
    if ($keyword === "" || stripos($p["name"], $keyword) !== false || stripos($p["desc"], $keyword) !== false) {
        $results[$id] = $p;
    }
}

/* 4. Order Form */
$errors = [];
$order  = null;

$selected = 0;
if (isset($_POST["product"])) {
    $selected = (int) $_POST["product"];
} elseif (isset($_GET["buy"])) {
    $selected = (int) $_GET["buy"];
}

$old = [
    "name"  => "",
    "email" => "",
    "qty"   => 1,
    "note"  => "",
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $old["name"]  = trim($_POST["name"] ?? "");
    $old["email"] = trim($_POST["email"] ?? "");
    $old["qty"]   = (int) ($_POST["qty"] ?? 0);
    $old["note"]  = trim($_POST["note"] ?? "");

    if ($old["name"] === "") {
        $errors[] = "Enter your name.";
    }
    if (!filter_var($old["email"], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Enter a valid email address, for example name@mail.com.";
    }
    if (!isset($products[$selected])) {
        $errors[] = "Choose a product from the list.";
    }
    if ($old["qty"] < 1 || $old["qty"] > 99) {
        $errors[] = "Quantity must be between 1 and 99.";
    }

    if (count($errors) === 0) {
        $price    = $products[$selected]["price"];
        $subtotal = $price * $old["qty"];

        if ($old["qty"] >= 5) {
            $discount = $subtotal * 0.10;
        } else {
            $discount = 0;
        }

        $order = [
            "name"     => $old["name"],
            "email"    => $old["email"],
            "product"  => $products[$selected]["name"],
            "price"    => $price,
            "qty"      => $old["qty"],
            "note"     => $old["note"],
            "subtotal" => $subtotal,
            "discount" => $discount,
            "total"    => $subtotal - $discount,
            "time"     => date("d M Y, H:i") . " WIB",
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tung Sahoor | Handmade Woodcrafts</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Alfa+Slab+One&family=Figtree:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <header class="site-header">
    <div class="wrap site-header__inner">
      <a class="logo" href="#home">TUNG SAHOOR</a>
      <nav class="nav" aria-label="Main navigation">
        <a href="#home">Home</a>
        <a href="#about">About</a>
        <a href="#products">Products</a>
        <a href="#order">Order</a>
        <a href="#contact">Contact</a>
      </nav>
    </div>
  </header>

  <main>

    <!-- HOME -->
    <section class="hero" id="home">
      <div class="wrap hero__inner">
        <div class="hero__text">
          <h1 class="hero__title">
            <span>Tung</span>
            <span>Tung</span>
            <span>Tung</span>
            <span>Sahoor</span>
          </h1>
          <p class="hero__lead">Handmade woodcrafts with an aesthetic and eco-friendly touch.</p>
          <div class="hero__actions">
            <a class="btn btn--primary" href="#products">See products</a>
            <a class="btn btn--ghost" href="#order">Order now</a>
          </div>
        </div>
        <div class="hero__frame">
          <img src="https://akcdn.detik.net.id/community/media/visual/2025/05/18/tung-tung-tung-sahur-1747556866558_169.jpeg?w=700&q=90" alt="Tung Tung Tung Sahur, a wooden night-watch club with a face and legs">
        </div>
      </div>
    </section>

    <!-- ABOUT -->
    <section class="section" id="about">
      <div class="wrap about">
        <div class="about__text">
          <h2>About Tung Sahoor</h2>
          <p>Tung Sahoor is a woodcraft shop named after Tung Tung Tung Sahur, an Indonesian internet character: a wooden night-watch club with a face and legs. We make handmade pieces from wood with an aesthetic, eco-friendly touch.</p>
          <ul class="why">
            <li>Made from wood</li>
            <li>Handmade, piece by piece</li>
            <li>Eco-friendly touch</li>
          </ul>
        </div>

        <div class="about__story">
          <h3>How the character started</h3>
          <ol class="timeline">
            <li>
              On 28 February 2025, a day before Ramadan began in Indonesia, TikTok creator Noxa asked DALL-E to "draw a pentungan sahur with a face and legs."
            </li>
            <li>
              He kept prompting: "neighborhood watch post kentungan," "neighborhood watch post club," and "a patrol pentungan with a single human leg."
            </li>
            <li>
              He posted his favorite image on TikTok as part of the "Anomali AI character" trend, an Indonesian offshoot of Italian brainrot: absurdist AI-generated beings with strange names, mock-lore, and repetitive audio.
            </li>
          </ol>
        </div>
      </div>
    </section>

    <!-- PRODUCTS -->
    <section class="section section--alt" id="products">
      <div class="wrap">
        <h2>Goods and Services</h2>

        <form class="search" method="GET" action="index.php#products">
          <label class="sr-only" for="q">Search products</label>
          <input class="field" type="search" id="q" name="q" placeholder="Search products, e.g. angklung" value="<?php echo e($keyword); ?>">
          <button class="btn btn--dark" type="submit">Search</button>
        </form>

        <?php if ($keyword !== "") : ?>
          <p class="search__info">
            <?php echo count($results); ?> result<?php echo count($results) === 1 ? "" : "s"; ?> for
            <strong><?php echo e($keyword); ?></strong>.
            <a href="index.php#products">Clear search</a>
          </p>
        <?php endif; ?>

        <?php if (count($results) === 0) : ?>
          <p class="empty">No product matches "<?php echo e($keyword); ?>". Try "tung" or <a href="index.php#products">show all products</a>.</p>
        <?php else : ?>
          <ul class="grid">
            <?php foreach ($results as $id => $p) : ?>
              <li class="product">
                <img src="<?php echo e($p["img"]); ?>" alt="<?php echo e($p["name"]); ?>" loading="lazy">
                <div class="product__body">
                  <h3><?php echo e($p["name"]); ?></h3>
                  <p><?php echo e($p["desc"]); ?></p>
                  <div class="product__foot">
                    <span class="price"><?php echo money($p["price"]); ?></span>
                    <a class="btn btn--small" href="index.php?buy=<?php echo $id; ?>#order">Order this</a>
                  </div>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    </section>

    <!-- ORDER -->
    <section class="section section--dark" id="order">
      <div class="wrap order">
        <div class="order__main">
          <h2>Order</h2>

          <?php if ($order !== null) : ?>
            <div class="receipt" role="status">
              <h3>Order received</h3>
              <table>
                <tr><th>Name</th><td><?php echo e($order["name"]); ?></td></tr>
                <tr><th>Email</th><td><?php echo e($order["email"]); ?></td></tr>
                <tr><th>Product</th><td><?php echo e($order["product"]); ?></td></tr>
                <tr><th>Price each</th><td><?php echo money($order["price"]); ?></td></tr>
                <tr><th>Quantity</th><td><?php echo $order["qty"]; ?></td></tr>
                <tr><th>Note</th><td><?php echo $order["note"] !== "" ? e($order["note"]) : "-"; ?></td></tr>
                <tr><th>Subtotal</th><td><?php echo money($order["subtotal"]); ?></td></tr>
                <tr>
                  <th>Discount</th>
                  <td>
                    <?php if ($order["discount"] > 0) : ?>
                      -<?php echo money($order["discount"]); ?> (10% off for 5 or more)
                    <?php else : ?>
                      None
                    <?php endif; ?>
                  </td>
                </tr>
                <tr class="receipt__total"><th>Total</th><td><?php echo money($order["total"]); ?></td></tr>
                <tr><th>Received</th><td><?php echo e($order["time"]); ?></td></tr>
              </table>
            </div>
          <?php endif; ?>

          <?php if (count($errors) > 0) : ?>
            <div class="alert" role="alert">
              <strong>The order was not sent. Fix this and try again:</strong>
              <ul>
                <?php foreach ($errors as $error) : ?>
                  <li><?php echo e($error); ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <form method="POST" action="index.php#order">
            <label for="name">Name</label>
            <input class="field" type="text" id="name" name="name" value="<?php echo e($old["name"]); ?>" required>

            <label for="email">Email</label>
            <input class="field" type="email" id="email" name="email" value="<?php echo e($old["email"]); ?>" required>

            <label for="product">Product</label>
            <select class="field" id="product" name="product" required>
              <option value="">Choose a product</option>
              <?php foreach ($products as $id => $p) : ?>
                <option value="<?php echo $id; ?>" <?php echo $selected === $id ? "selected" : ""; ?>>
                  <?php echo e($p["name"]); ?> (<?php echo money($p["price"]); ?>)
                </option>
              <?php endforeach; ?>
            </select>

            <label for="qty">Quantity</label>
            <input class="field" type="number" id="qty" name="qty" min="1" max="99" value="<?php echo (int) $old["qty"]; ?>" required>

            <label for="note">Note (optional)</label>
            <textarea class="field" id="note" name="note" rows="3"><?php echo e($old["note"]); ?></textarea>

            <button class="btn btn--primary" type="submit">Place order</button>
          </form>
        </div>

        <aside class="order__side">
          <h3>Price list</h3>
          <table class="pricelist">
            <thead>
              <tr><th>Product</th><th>Price</th></tr>
            </thead>
            <tbody>
              <?php foreach ($products as $p) : ?>
                <tr><td><?php echo e($p["name"]); ?></td><td><?php echo money($p["price"]); ?></td></tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <p>Order 5 or more of the same item and get 10% off.</p>
        </aside>
      </div>
    </section>

    <!-- CONTACT -->
    <section class="section" id="contact">
      <div class="wrap">
        <h2>Contact</h2>
        <p>Questions about a piece or a custom order? Write to us.</p>
        <p class="contact__line">
          <strong>Email</strong>
          <a href="mailto:tungtungtung@sahoor.com">tungtungtung@sahoor.com</a>
        </p>
        <p class="contact__line">
          <strong>Learn more</strong>
          <a href="https://en.wikipedia.org/wiki/Tung_Tung_Tung_Sahur" target="_blank" rel="noopener">Tung Tung Tung Sahur on Wikipedia</a>
        </p>
      </div>
    </section>

  </main>

  <footer class="site-footer">
    <div class="wrap site-footer__inner">
      <p class="views">Total Page Views: <?php echo $views; ?></p>
      <p class="credit">Tung Tung Tung Sahoor | S</p>
    </div>
  </footer>

</body>
</html>
