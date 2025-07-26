### Project Scaffold PHP
**Informações gerais sobre o projeto**

### Pre-requisitos

- Docker
- Docker-compose

### Tecnologias utilizadas

- PHP `8.2`
- Mysql `10`

### PORTAS

|Container| Porta |
|---------|-------|
|  `PHP`  |  `80` |
| `Mysql` |`3306` |
|`Adminer`|`8080` |

#### Configurações de ambiente

No **env.ini** estão as configurações de ambiente do projeto

#### Routes do produto

|Métodos| Parâmetros | Descrição |
|---|---|---|
|`GET`| `/` | Example page. |

### Iniciar projeto

```bash
  docker-compose up -d
```
