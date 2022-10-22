# Introduction

Rovota Core is a modern PHP framework created to make it trivial to build fast, secure and feature-rich applications. With built-in support for most things you'd need, writing code becomes a painless experience that lets you focus on the things that matter.

### Backstory

When we set out to build our main (unreleased) product, we decided against using an existing framework due to a variety of reasons. One of those reasons is that all of them needed a vast amount of work to make them fit our product's needs.

At the time, we evaluated an approach of having a single, fully integrated codebase. This however, would limit compatibility with all existing libraries out there, so we decided to split the codebase in two parts: a framework built to suit our needs and a layer on top that would provide product specific functionality.

This approach allowed us to re-use the framework for other future products as well as our own website, account portal and more, saving us a lot of development resources down the line.

After the decision to build our own in-house framework, we felt that there may be other projects that would benefit from our work, so we decided to develop it into something that is more versatile and usable as a standalone framework rather than being a proprietary creation.

### Differentiation

While there are certainly other great frameworks out there, like Laravel and Symfony, they may not be the best fit for every application. Core is created to be a more suitable framework for those who wish to build web applications with installable modules, minimal performance overhead, built-in two-factor authentication, [HIBP](https://haveibeenpwned.com/About), localized content and support for the latest PHP version.

### Familiarity

When browsing through our documentation, you may encounter various familiarities with other open source frameworks out there. Some of which is intentional, since it makes absolutely no sense to use different terminology or change how functionality works for the sake of being different. Throughout our codebase, we have credited other projects in order to be fair towards other people's efforts.

This familiarity also allows developers to switch to Core, as well as the other way around, without having to learn everything from scratch, even though it may be implemented differently under the hood.

### Compatibility

Core is designed in a way that allows continuous development of the codebase while minimally impacting compatibility with previously written code.

We do however tend to upgrade our codebase to work with new PHP versions faster than is common in the current PHP landscape. This is done to avoid being the dependency that prevents you from making use of new language features and improvements when they become available.
