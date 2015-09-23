FORMAT: 1A

# Scheduler API

This is the Scheduler API.

# Group Employee

## Get shifts assigned to employee [GET /employees/{id}/shifts]
- As an employee, I want to know when I am working, by being able to see all of the shifts assigned to me.
+ Parameters
    + id: 2 (required, number) - ID of the Employee in form of an integer
+ Request
    + Headers

            X-Access-Token: i_am_an_employee

+ Response 200 (application/json)

    ```
    {
        "shifts": [
            {
                "break": 0.5,
                "employee": {
                    "email": "ricky@roma.com",
                    "id": 2,
                    "name": "Richard Roma",
                    "phone": "312-331-3322"
                },
                "end_time": "Mon, 07 Sep 2015 23:30:00 -0500",
                "id": 1,
                "manager": {
                    "email": "jwilliamson@gmail.com",
                    "id": 1,
                    "name": "John Williamson",
                    "phone": "312-332-1233"
                },
                "start_time": "Mon, 07 Sep 2015 17:30:00 -0500"
            },
            {
                ...
            }
        ]
    }
    ```

## Get shift details [GET /shifts/{id}{?with_coworkers}]
- As an employee, I want to know who I am working with, by being able to see the employees that are working during the same time period as me.
- As an employee, I want to be able to contact my managers, by seeing manager contact information for my shifts.
+ Parameters
    + id: 2 (required, number) - ID of the Shift in form of an integer
    + with_coworkers (optional, boolean) - Get employees that are working at the same time
        + Default: false
+ Request
    + Headers

            X-Access-Token: i_am_an_employee

+ Response 200 (application/json)

    ```
    {
        "shift": {
            "break": 0.5,
            "coworkers": [
                {
                    "email": "oldguy@aol.com",
                    "id": 3,
                    "name": "Shelly Levene",
                    "phone": "312-331-1212"
                },
                {
                    ...
                }
            ],
            "employee": {
                "email": "ricky@roma.com",
                "id": 2,
                "name": "Richard Roma",
                "phone": "312-331-3322"
            },
            "end_time": "Tue, 08 Sep 2015 23:30:00 -0500",
            "id": 2,
            "manager": {
                "email": "jwilliamson@gmail.com",
                "id": 1,
                "name": "John Williamson",
                "phone": "312-332-1233"
            },
            "start_time": "Tue, 08 Sep 2015 17:30:00 -0500"
        }
    }
    ```

## Get summary of hours worked in week [GET /employees/{id}/hours/weekly{?date}]
- As an employee, I want to know how much I worked, by being able to get a summary of hours worked for each week.
+ Parameters
    + id: 2 (required, number) - ID of the Employee in form of an integer
    + date: `2015-09-10` (required, string) - an RFC 3339 formatted date string
+ Request
    + Headers

            X-Access-Token: i_am_an_employee

+ Response 200 (application/json)

    ```
    {
        "summary": {
            "end": "Sun, 13 Sep 2015 00:00:00 -0500",
            "hours": 16.5,
            "start": "Mon, 07 Sep 2015 00:00:00 -0500"
        }
    }
    ```

# Group Manager

## Create a shift [POST /shifts]
- As a manager, I want to schedule my employees, by creating shifts for any employee.
+ Request (application/json)
    + Headers

            X-Access-Token: i_am_an_employee

    + Body
    ```
    {
        "start": "Wed, 09 Sep 2015 09:00:00 -0500",
        "end": "Wed, 09 Sep 2015 17:00:00 -0500",
        "break": 0.75
    }
    ```

+ Response 201 (application/json)
    + Headers

            Location: /shifts/8

    + Body
    ```
    {
        "shift": {
            "break": 0.75,
            "employee": {
                "email": null,
                "id": null,
                "name": null,
                "phone": null
            },
            "end_time": "Wed, 09 Sep 2015 17:00:00 -0500",
            "id": 8,
            "manager": {
                "email": "jwilliamson@gmail.com",
                "id": 1,
                "name": "John Williamson",
                "phone": "312-332-1233"
            },
            "start_time": "Wed, 09 Sep 2015 09:00:00 -0500"
        }
    }
    ```

## Get shifts in time period [GET /shifts{?start,end}]
- As a manager, I want to see the schedule, by listing shifts within a specific time period.
+ Parameters
    + start: `2015-09-07T00:00:00-05:00` (required, string) - an RFC 3339 formatted date string
    + end: `2015-09-13T00:00:00-05:00` (required, string) - an RFC 3339 formatted date string
+ Request
    + Headers

            X-Access-Token: i_am_a_manager

+ Response 200 (application/json)
    ```
    {
        "shifts": [
            {
                "break": 0.5,
                "employee": {
                    "email": "ricky@roma.com",
                    "id": 2,
                    "name": "Richard Roma",
                    "phone": "312-331-3322"
                },
                "end_time": "Mon, 07 Sep 2015 23:30:00 -0500",
                "id": 1,
                "manager": {
                    "email": "jwilliamson@gmail.com",
                    "id": 1,
                    "name": "John Williamson",
                    "phone": "312-332-1233"
                },
                "start_time": "Mon, 07 Sep 2015 17:30:00 -0500"
            },
            {
                ...
            }
        ]
    }

    ```

## Update a shift [PUT /shifts/{id}]
- As a manager, I want to be able to change a shift, by updating the time details.
- As a manager, I want to be able to assign a shift, by changing the employee that will work a shift.
+ Parameters
    + id: 8 (required, number) - ID of the Shift in form of an integer
+ Request (application/json)
    + Headers

            X-Access-Token: i_am_an_employee

    + Body
    ```
    {
        "employee_id": 3,
        "start": "2015-09-09T10:00:00",
        "end": "2015-09-09T18:00:00",
        "break": 0.75
    }
    ```

+ Response 200 (application/json)
```
{
    "shift": {
        "break": 0.75,
        "employee": {
            "email": "oldguy@aol.com",
            "id": 3,
            "name": "Shelly Levene",
            "phone": "312-331-1212"
        },
        "end_time": "Tue, 22 Sep 2015 10:35:01 -0500",
        "id": 8,
        "manager": {
            "email": "jwilliamson@gmail.com",
            "id": 1,
            "name": "John Williamson",
            "phone": "312-332-1233"
        },
        "start_time": "Tue, 22 Sep 2015 10:35:01 -0500"
    }
}
```

## Get employee details [GET /employees/{id}]
- As a manager, I want to contact an employee, by seeing employee details.
+ Parameters
    + id: 2 (required, number) - ID of the Employee in form of an integer
+ Request
    + Headers

            X-Access-Token: i_am_a_manager

+ Response 200 (application/json)
```
{
    "employee": {
        "email": "ricky@roma.com",
        "id": 2,
        "name": "Richard Roma",
        "phone": "312-331-3322"
    }
}
```
