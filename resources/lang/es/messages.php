
<?php
return [

    //footer
    'Copyright' => "Copyright &copy; ".date('Y')." ".Config::get('app.name').". All Rights Reserved.",

    //verification
    'notVerified' => "El número de verificación no existe",
    'verified' => "Tu cuenta ha sido verificada",
    'wasVerified' => "Tu cuenta ya ha sido verificada con anterioridad",

    //emails
    'emailVerification' => "Se requiere verificación de correo electrónico",
    'emailReset' => "Nueva Contraseña",
    'emailWelcome' => "Bienvenido a ".Config::get('app.name')."",
    'selectFormRequired' => "Por favor ingresa un valor correcto",
    'emailSubscribeSubject' => "Tiene un nuevo subscriptor.",
    'emailSubscribeBody' => "Tienes un nuevo subscriptor. Correo: ",
    //email reset password
    'emailResetText1' => "You are receiving this email because we received a password reset request for your account.",
    'emailResetText2' => "Reset Password",
    'emailResetText3' => "If you did not request a password reset, no further action is required.",
    'emailResetText4' => "If you’re having trouble clicking the button, copy and paste the URL below into your web browser:",
    //email Administration
    'emailAdminTitle' => "Email's Personalizados",
    'emailAdminTarget' => "Objetivo de alcance",
    'emailAdminSelect' => "Selecciona tu objetivo",
    'emailPrioritySelect' => "Selecciona tu prioridad",
    'emailAdminAllUsers' => "Todos los usuarios",
    'emailAdminSubscribers' => "Todos los suscriptores",
    'emailAdminSubject' => "Asunto",
    'emailAdminPrioridad' => "Prioridad",
    'emailAdminIdioma' => "Idioma",
    'emailAdminBody' => "Cuerpo del mensaje (incluye cabezera, pie, saludos y despedida)",
    'emailAdminAllTipo1' => "Todos los usuarios",
    'emailAdminAllTipo2' => "",
    'subjectFormRequired' => "Por favor ingresa un correcto asunto",
    'bodyFormRequired' => "Por favor ingresa un correcto cuerpo de mensaje",
    'emailPrioridad1' => "Alta",
    'emailPrioridad3' => "Media",
    'emailPrioridad5' => "Baja",

    //libraries
    'successTrue' => "TRUE",
    'successFalse' => "FALSE",
    'admin' => "ADMIN",
    'prioridadWelcome' => '3',
    'prioridadReset' => '1',
    'prioridadVerificationCompare' => '3',
    'prioridadPswd' => '3',
    'prioridadCustom' => '5',

    //BD Messages and Errors
    'BDempty' => "Base de datos vacía",
    'BDdataCheck' => "Todo se revisó correctamente",
    'BDdata' => "Hay datos en la base de datos",
    'BDnoData' => "No hay datos iguales en la base de datos",
    'BDsuccess' => "La información se actualizó correctamente",
    'errorsBD' => "Hubo un error por favor contacte al administrador o revise su conexión a internet",
    'errorsBDFail' => "Correo o Contraseña incorrecta",
    'errorsBDMailFail' => "Correo o Contraseña incorrecta",
    'errorsBDRepeat' => "Estos datos ya existen",
    'errorsBDRepeatMail' => "Ese correo electrónica ya existe. Intenta recuperar tu contraseña.",
    'errorFormat' => "El archivo que desea cargar tiene un formato no válido.",
    'BDsentMail' => "Te hemos enviado un correo electrónico con tu nueva contraseña..",
    'SendSMS' => "Código de verificación enviado correctamente.",
    'errorsSendSMS' => "Error al enviar código de verificación.",
    'SendSMS2' => "Mensaje enviado correctamente.",
    'errorsSendSMS2' => "Error al enviar mensaje.",
    'VerifiedCode' => "El código de verificación es correcto.",
    'errorsVerifiedCode' => "El código de verificación es incorrecto.",
    'errorsCedula' => "La cédula profesional no existe.",

];
