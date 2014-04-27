Partials should always included as follows

```
include('subdir_1/subdir_2/.../subdir_n/template.twig')
```

The current directory is Twig's template root path which means that your template path must start with a subdirectory of this folder. As every path has always to be explicitly defined it's always clear from what location you include the template. It's also way easier to move template files and change the full paths as it would be with different templates that had the same name. If relative paths would be allowed your IDE wouldn't update the template references correctly.

Example
-------

```
include('application/endpoint/index-section-create-endpoint.twig')
```