
## Enable notifications

![image](https://cloud.githubusercontent.com/assets/1245473/16138621/cd46a8ba-343f-11e6-870f-9ddb92f29d07.png)

To receive notifications, a user has to enable the specific Semantic MediaWiki
related notification [preferences][pref] with an agent (the user that initiated a change) being
generally excluded from receiving notifications about his own actions.

## Workflow

![image](https://cloud.githubusercontent.com/assets/1245473/18805668/7cb694a0-8214-11e6-952d-e84e98fa6899.png)

### Monitor value changes

To monitor property value changes (yellow icon), it is required that a property:

- Denotes a `Notifications on` property with `+` to identify any value change
  while a distinct value like `[[Notifications on::distinctValue]]` will only
  create a notification event for a change from/to that "distinctValue"
- Declares a `Notifications to group` to inform members of that group in case
  of a notification event

After preferences have been set, properties appropriately prepared, then each
user can decided to add on his/her user page a membership to one or more
notification groups using the `Notifications group member of` property.

- `SomeProperty` → `[[Notifications on::+]]` → `[[Notifications to group::Bar]]`
- `User:Foo` → `[[Notifications group member of::Bar]]`

It is not possible to add a membership `Notifications group member of` to a group
that has not yet been assigned to any property.

The same procedure can be applied to watch categories where `Notifications on`
and `Notifications to group` are being denoted on a category page and will trigger
an event in case a page adds/removes that category.

### Monitor specification changes

If a user wants to watch changes to properties itself (blue icon) then `Notifications group member of`
provides a special group called `entity specification change group` (which is
reserved) to monitor changes to all property pages.

[pref]: https://www.mediawiki.org/wiki/Help:Notifications#Preferences_and_settings