<html data-theme="light">
<head>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
  <style>
    body {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh; 
      margin: 0;
      background-color: #f0f0f0; 
    }
    .main {
      width: 300px; 
      padding: 1.5rem;
      background: #ffffff; 
      border-radius: 8px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
    }
    h1.title {
      text-align: center;
      font-size: 1.5rem;
      margin-bottom: 1rem;
    }
    .button.is-success {
      width: 100%; 
    }
    .field {
      margin-bottom: 1rem; 
    }
  </style>
</head>
<body>
  <div class="main container">
    <h1 class="title">Iniciar Sesión Sistema RADCS</h1>
    <form action="" method="POST" autocomplete="off">
      <div class="field">
        <p class="control has-icons-left has-icons-right">
          <input class="input" type="correo" name="correo" placeholder="correo" maxlength="80" required>
          <span class="icon is-small is-left">
            <i class="fas fa-envelope"></i>
          </span>
        </p>
      </div>
      <div class="field">
        <p class="control has-icons-left">
          <input class="input" type="contrasena" name="contrasena" placeholder="contrasena" pattern="[a-zA-Z0-9$@.-]{7,30}" maxlength="30" required>
          <span class="icon is-small is-left">
            <i class="fas fa-lock"></i>
          </span>
        </p>
      </div>
      <div class="field">
        <p class="control">
          <button type="submit" class="button is-success">
            Login
          </button>
        </p>
      </div>
      <?php
			if(isset($_POST['correo']) && isset($_POST['contrasena'])){
				require_once "php/main.php";
				require_once "php/iniciar_sesion.php";
			}
		?>
    </form>
  </div>
</body>
</html>