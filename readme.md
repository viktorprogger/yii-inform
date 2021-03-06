##Roadmap:

- [x] *[release-blocker]* Вынести форматирование кнопок в отдельный класс
- [x] *[release-blocker]* Переиспользовать это форматирование в других экшенах
- Доделать `TelegramMessageGenerator`: 
  - [x] *[release-blocker]* Разобраться с кнопками
  - [x] *[release-blocker]* За-DTO-шить гитхабовские пейлоуды и доделать тексты сообщений в генераторе (в DTO засовывать в иге не решился)
  - [ ] *[release-blocker]* Добавить удаление репозитория, этот кейс обрабатывается в `LoadRepositoriesCommand`
- [x] *[release-blocker]* Протестировать получившееся
- [ ] *[release-blocker]* Сделать генерацию саммари-сообщения
- [ ] *[release-blocker]* Сделать подписку на все сразу репы и отписку сразу ото всех
- Настраивать, на какие именно события юзер подписан.
  - [ ] *[release-blocker]* Суммарно по realtime
  - [ ] *[release-blocker]* Суммарно по summary
  - [ ] Отдельно для каждого репозитория внутри настроек realtime/summary. Если для репы такое настроено, то эта настройка приоритетнее предыдущих двух. Этот пункт - на после релиза.
- [ ] *[release-blocker]* Сделать уведомление о мерже PR, сейчас это просто закрытие PR.
- [ ] *[release-blocker]* Прикрутить Sentry
- [ ] Сделать паузу. Режим, в котором не меняются настройки подписок, но и не отправляются никакие уведомления.
- [ ] Настройка частоты и времени отправки summary
- [ ] Настройка часового пояса для времени отправки summary
- Добавить сообщения о принятии пожертвований любых сумм
  - [ ] Принять решение о том, в каком виде, где и когда эти сообщения будут видны 
  - [ ] Прикладывать ссылку на OpenCollective
  - [ ] Принимать в криптовалюте (сперва посоветоваться с samdark)
- [ ] Добавить подписку авторам на события по их PR/Issue. Чтобы приходили сообщения о провалившихся тестах в PR, комментариях в нем или issue, о мерже, смене лейблов и т.п. Т.е. юзер телеги должен будет указать свой гитхабовский юзернейм.
- [ ] Локализация, перевод на английский язык и использование локализованных текстов в целом, сохранение локали юзера.
- Дополнительные режимы формата summary
  - [ ] Не присылать realtime события ночью, а утром присылать summary по всем пропущенным
  - [ ] По кнопке присылать summary тех событий, что произошли после предыдущего получения summary
  - [ ] Summary за указанный период времени
  - [ ] Настройка частоты автоматически присылаемых summary
