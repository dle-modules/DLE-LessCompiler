# DLE-LessCompiler
![version](https://img.shields.io/badge/version-3.0.0-red.svg?style=flat-square "Version")
![DLE](https://img.shields.io/badge/DLE-8.x--10.x-green.svg?style=flat-square "DLE Version")
[![MIT License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](https://github.com/dle-modules/DLE-LessCompiler/blob/master/LICENSE)

DLE-LessCompiler — LESS компилятор для CMS DataLife Engine (8.x - 10.x)

### Ссылки
[Автор модуля](http://pafnuty.name/ "ПафНутиЙ")

[Рпозиторий класса less.php](https://github.com/oyejorge/less.php "lessю.php")

[Официальный сайт LESS](http://lesscss.org/ "Официальный сайт LESS")

## Для чего это?
- Для нормального использования LESS при вёрстке под CMS DLE.
- И как следствие - для удобной, быстрой и эффективной разработки сайта.

## Возможности
- Автоматическая компиляция less при изменении файла, при этом отслеживаются изменения и в импортированных файлах.
- Возможность минификации css файла.
- Возможность генерировать sourseMap файл.
- Наглядный вывод ошибок компиляции.

## Установка
- Загрузить содержимое папки **upload** в корень сайта.
- В начале main.tpl прописать `{include file="engine/modules/less/getscc.php"}`
- По умолчанию подключается файл main.less из папки **less** текущего шаблона сайта, а в папку **css** текущего шаблона записывается одноимённый css-файл.

## Настройка
Для указания собственных настроек компиляции пишем нужные параметры в строке подключения.
Если необходимо настроить внешний вид сообщения об ошибках компиляции - это делается в файле **-less-error.css**, расположенном в папке templates/Default/css (Не забывайте переместить этот файл в свой шаблон сайта).

### Параметры строки подключения
- `localSpaceFolder` — путь от корня сайта к папке, содержащей папку less (в которой лежат файлы)
- `files` — имена файлов (если их несколько), которые нужно скомпилить, через запятую.
- `outputPath` — путь к папке с css
- `compress` — минифицировать css-файл.
- `sourceMap` — Генерировать  sourceMap.
