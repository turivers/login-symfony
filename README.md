Login Symfony Test Task
=================

Требования
----------

  * PHP 7.1.3 or higher;

Установка
---------
```bash
$ git clone https://github.com/turivers/login-symfony.git
$ composer install
```

Функциональные требования к системе:
------------------------------------

1. Форма входа должна содержать два текстовых поля для ввода имени пользователя и пароля.
2. В случае успешного входа должна быть показана страница пользователя.
3. Страница пользователя должна содержать сообщение: «Добрый день, <имя пользователя>» и кнопку выхода из системы.
4. При нажатии на кнопку выхода должна открываться страница входа.
5. В случае неуспешного входа должна быть показана страница входа с сообщением: «Неверные данные».
6. После успешного входа страница входа не должна быть доступна, пользователь должен быть перенаправлен на страницу пользователя.
7. Страница пользователя не должна быть доступна, если вход не выполнен. Пользователь должен
быть перенаправлен на страницу входа.
8. В случае 3-х неуспешных попыток входа подряд система должна быть заблокирована на 5 минут,
при этом при попытке входа должно выводиться сообщение: «Попробуйте еще раз через <N>
секунд».
