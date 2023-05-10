*************
откат - https://itproger.com/course/git/3
получить список комитов
git log --oneline

просмотреть какой-либо коммит выполните комманду: git checkout fa2de7b  . сб утро - перед пинией

*************
https://r-color.ru/tools/ipm/?url=/&type=news&action=all
*************

Удалить или переименовать папку с проектом
зайти на гит хаб и скопировать урл репозитория
git@github.com:derzunov/ipm.git

Выполнить команду клонирования с сервера на локальный
d:\
git clone git@github.com:derzunov/ipm.git ipm

запустить шторм
переключиться в нужную ветку
стянуть с гита свежие файлы

проверить настройки шторма - может отключиться настройка eslint

ВАЖНО сделать npm i через git-bash тк phpmorphy тока через него ставится!!

// https://www.npmjs.com/package/phpmorphy
// https://github.com/antixrist/node-phpmorphy

// ВАЖНО! перед работой проверить наличие файлов в каталоге
// d:\brand-core\node_modules\phpmorphy\dist\
// чтобы они появились нужно загрузить библиотеку через git-bash
// в windows нужно перейти в каталог проекта. нажать правую кнопку мыши (не на файле или каталога - на пустом месте) - появится контекстное меню.
// в нем будет Git Bash here - запустится в текущем каталоге проекта.
// а затем npm install phpmorphy в этом случае в каталоге dist появятся желанные скомпилированные файлы



После клонирования текущая ветка - master
( чтобы создать новую ветку git checkout -b dev-v )
git checkout origin develop // переключиться локально в develop, которой локально еще нет



!!!!!!!!!!!!!!!!!!!!!!!!!
git checkout develop // переключиться локально в develop
git pull develop // скачать с сервера из develop
git checkout dev-v // переключиться в свою ветку
git merge develop // смержить в свою ветку из локального develop
!!!!!!!!!!!!!!!!!!!!!!!

****************
Работа со стеком
git stash
git stash pop
****************


*************

git fetch --all  // все ветки получить локально



забрать из develop, только если собираюсь мержить
разработать фичу

https://www.atlassian.com/ru/git/tutorials/install-git
https://git-for-windows.github.io/
git --version


настройка репозитория
https://www.atlassian.com/ru/git/tutorials/setting-up-a-repository


ssh-keygen / создать локальный сертификат
см в c:\Users\user\.ssh\id_rsa.pub  
сожержимое скопировать в https://github.com/settings/keys
предварительно удалить старый ключ для данного компа


git clone git@github.com:derzunov/r-color-vuetailwind.git YOUR_FOLDER
git clone git@github.com:derzunov/r-color-vuetailwind.git vuetailwind

в процессе клонирования, если спросит про known hosts - набрать  yes
после клонирования оказываемся в ветке master


git status / показывает в текущей ветке незакомиченные изменения

git checkout dev-v / переключиться в ветку xxx

git pull / получить изменения для текущей ветки с сервера

git add -A / добавить в гит все новые файлы (тогда он будет их отслеживать)

!!! пред merge следует сделать pull для xxx и текущей ветки
git merge xxx / замержить ветку xxx в текущую ветку

git push / отправить закомиченные изменения текущей ветки на сервер в соотв ветку

git branch -D dev-v // удалить локальную ветку
git branch -rD dev-v // удалить ветку на сервере


git branch // без параметров - список доступных веток. звездочкой отмечена текущая




https://danielkummer.github.io/git-flow-cheatsheet/index.ru_RU.html
https://www.atlassian.com/ru/git/tutorials/comparing-workflows/gitflow-workflow


ВАЖНО
чтобы не было неразберихи с CRLF и LF - читаем
https://stackoverflow.com/questions/53516594/why-do-i-keep-getting-delete-cr-prettier-prettier

правим
.eslintrc.js

-------------
чтобы создать локальный сертификат - запустить в командной строке
ssh-keygen
ничего не вводить - пройти все вопросы энтером

выведет...
Microsoft Windows [Version 10.0.19044.2251]
(c) Корпорация Майкрософт (Microsoft Corporation). Все права защищены.

c:\Users\user>ssh-keygen
Generating public/private rsa key pair.
Enter file in which to save the key (C:\Users\user/.ssh/id_rsa):
Enter passphrase (empty for no passphrase):
Enter same passphrase again:
Your identification has been saved in C:\Users\user/.ssh/id_rsa.
Your public key has been saved in C:\Users\user/.ssh/id_rsa.pub.
The key fingerprint is:
SHA256:7Wgc4DslP3Y4hR2tkIPnOsU4t/f2IONuryMZUshs7sk user@C205
The key's randomart image is:
+---[RSA 3072]----+
|                 |
|       . . .     |
|     oo.= . .    |
|     .=*.* o     |
|     o=.S =      |
|      o@.B       |
|     o=o@o* .    |
|      E=o*o+..   |
|         ++=o..  |
+----[SHA256]-----+

c:\Users\user>

результатом будет файл id_rsa.pub
его содержимое:
ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABgQsdghdsfDFHDSDumDSHSGH5zcl0tKw7CjtSoHs940TosX5раыg24VoT15pPbEg78C0unAEcxJVtQQ+ixGExRBGdindBkPCaCkvYBfHuWjocMpa3g5ASvkYJPcTXHokvuM2ZyXN4ZLk3hPxdFNnQnDvo1gcjzMXxXWqziScNnig+Rtgc+5GDlaTN1vbEzH0DaKD6zog5nuL1HNk1iRD0GyoEoULV6+lPbUyTzMgT55Y3HIbqAVSBnq2bmgvq2bDpJMnGxedXM7fCPoHirQftwHU3t/UvBMYEmxCHxmjgnMyIkpHeifHAOykdckf4XtV8yKB/HoCWQnsqYP4l3jkfScrLnKGgXF4nnZqHj4FmH1nuh0kA9dxQk10ai1FRpComzFULk67CpGQ9L8n5iZCvn5jTM0ftPsA+MLHzBongV5XtzfyCOanR52XAVY/lyK6XAqq1sTQ9hjGAoJDHm0FonVBACD2de7/3N5Ofs0gRnkIUws= user@C205

https://github.com/settings/keys
ввести содержимое в SSH keys  - New SSH key
This is a list of SSH keys associated with your account. Remove any keys that you do not recognize.
Authentication Keys
ввести имя компа (С205) и значение ключа




