# ttrss plugins


* put in ttrss/plugins.local
* link af_comics-filters.local  plugins/af_comics/filters.local

## Testing a plugin:
Get feed id from pressing 'f D' on that feed.
```
sudo -u www-data php update.php --force-rehash --debug-feed 123
```

