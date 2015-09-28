# Scheduler API

[ ![Codeship Status](https://img.shields.io/codeship/16190e20-2a6c-0133-0222-622b866f1c07.svg)](https://codeship.com/projects/98269)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/b/rpalladino/scheduler-api/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/b/rpalladino/scheduler-api/?branch=master)

This is a demo application that allows the scheduling of shifts for employees (see [use cases](features/)). It is built using [Hexagonal architecture](http://alistair.cockburn.us/Hexagonal+architecture) in PHP, with an adapter for the RESTful HTTP port provided by [Radar](https://github.com/radarphp/Radar.Project), a PSR-7 compliant framework implementing the [Action-Domain-Responder](http://pmjones.io/adr) (ADR) pattern.

## Standards

+ [PSR-2 Coding Style](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) for PHP code
+ [API Blueprint](https://apiblueprint.org/) for API documentation
+ [API Problem](http://tools.ietf.org/html/draft-nottingham-http-problem-07) for providing error details in HTTP responses