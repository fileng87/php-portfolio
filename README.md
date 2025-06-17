# PHP 個人作品集網站

一個使用 PHP、MariaDB 和 Docker (Dev Containers) 建構的簡單個人作品集與留言板網站。

[倉庫地址](https://github.com/fileng87/php-portfolio)

## ✨ 主要功能

*   關於我
*   作品集展示
*   留言板

## 🚀 技術棧

*   PHP
*   MariaDB
*   HTML / CSS / JS
*   Docker / Docker Compose
*   VS Code Dev Containers

## 🛠️ 運行開發環境

1.  **安裝:** Docker Desktop, VS Code, VS Code Dev Containers 擴充功能。
2.  **啟動:** 使用 VS Code 打開專案，選擇 "Reopen in Container"。
3.  **運行:** 在容器的 VS Code 終端機中執行 `php -S 0.0.0.0:8000 -t .` (需先在 `devcontainer.json` 轉發 8000 端口)。
4.  **訪問網站:** `http://localhost:8000`
5.  **訪問 phpMyAdmin:** `http://localhost:8080` (需先在 `devcontainer.json` 轉發 8080 端口)，使用 `docker-compose.yml` 中的資料庫帳號密碼登入。

## ⚙️ 環境變數設定

本專案使用 `.env` 檔案來管理敏感的設定值，例如資料庫連線資訊。在運行開發環境或部署之前，您需要在專案根目錄下建立一個名為 `.env` 的檔案。

建議複製 `.env.example` (如果有的話) 或自行建立，並填入以下必要的變數：

```dotenv
# 資料庫設定
DB_NAME=portfolio   # 您想使用的資料庫名稱
DB_USER=user        # 資料庫使用者名稱
DB_PASSWORD=password  # 資料庫使用者密碼
DB_ROOT_PASSWORD=root_password # 資料庫 root 使用者密碼 (用於初始化)
```

**重要:** 請確保將上述範例中的 `portfolio`, `user`, `password`, `root_password` 替換為您自己的安全設定。

## 🚀 部署 (使用 Docker Compose)

除了使用 Dev Containers 進行開發，您也可以直接使用 Docker Compose 來部署此應用程式。

1.  **前置需求:** 確認您的系統已安裝 [Docker](https://docs.docker.com/get-docker/) 與 [Docker Compose](https://docs.docker.com/compose/install/)。
2.  **建置並啟動服務:** 在專案的根目錄下，開啟終端機並執行以下指令：
    ```bash
    docker-compose up -d --build
    ```
    *   `--build` 會在首次啟動或 Dockerfile 有變更時重新建置映像檔。
    *   `-d` 會讓容器在背景執行。
3.  **訪問網站:** 打開瀏覽器訪問 `http://localhost:80` (端口號取決於 `docker-compose.yml` 中的設定)。
4.  **停止服務:** 若要停止所有運行的容器，請在專案根目錄下的終端機執行：
    ```bash
    docker-compose down
    ```

## 📝 資料庫

*   使用 MariaDB，服務名為 `db`。
*   資料表結構由 `db/init.sql` 在首次啟動時初始化。
