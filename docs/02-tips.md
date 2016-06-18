
## Receive notifications about

### Property and category specification changes

The special group `entity specification change group` informs members about property specification
changes therefore apply for a membership by adding `[[Notifications group member of::entity specification change group]]`
to your user page.

### Pages added/removed to/from a specific category

To receive notifications about changes to a category, a category needs to signal that:

* It is generally being watched by `[[Notifications on::+]]`
* In case of a change (add/delete), members of `[[Notifications to group::foo group]]` are to receive
notifications

## Use cases

### Notify about project status change

Property:Project status

```
* [[Has type::Text]]
* [[Allows value::started]]
* [[Allows value::in progress]]
* [[Allows value::in-budget]]
* [[Allows value::over-budget]]
* [[Allows value::project manager was hospitalized]]

== Notification (escalation) matrix ==

{{#subobject:
 |Notifications on=started,in progress|+sep=,
 |Notifications to group=all project members, project management|+sep=,
}}{{#subobject:
 |Notifications on=over-budget
 |Notifications to group=project management,project controller group|+sep=,
}}{{#subobject:
 |Notifications on=project manager was hospitalized
 |Notifications to group=project controller group, senior project management group|+sep=,
}}

{{#ask: [[-Has subobject::{{FULLPAGENAME}}]]
 |?Notifications on
 |?Notifications to group
}}
```
If you need a more fain-grained policy as to when a specific project needs to send/suppress
a notification event/user, see the [hooks](hooks.md) document.

### Notify about changes to property types

Besides the global `entity specification change group`, a user can specifically watch any type
changes by:

Property:Has type

```
* [[Notifications on::+]]
* [[Notifications to group::property type change group]]
```

## Queries

### List all properties being watched

```
{{#ask:[[Notifications on::+]]
 |?Notifications on
 |?Notifications to group
 |format=broadtable
}}
```

### List all users with a group membership

```
{{#ask:[[Notifications group member of::+]]
 |?Notifications group member of
 |format=broadtable
}}
```