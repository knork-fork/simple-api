# Xdebug setup

Xdebug should be installed and setup if running docker-compose for non-prod (`docker-compose up -d`).

To run on production without Xdebug, use: 
```bash
docker-compose -f docker-compose.prod.yml up -d
```

#### VSCode setup

Add this to `.vscode/launch.json`:
```json
{
    "version": "0.2.0",
    "configurations": [
        {
            "name": "Listen for XDebug",
            "type": "php",
            "request": "launch",
            "port": 9003,
            "pathMappings": {
                "/application": "${workspaceRoot}"
            }
        }
    ]
}
```