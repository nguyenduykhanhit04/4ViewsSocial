API Gateway (Node.js + TypeScript)

API Gateway cho project microservices, dùng Node.js + Express + TypeScript để quản lý route và proxy tới các dịch vụ Auth, Chat, Admin, Post.

📦 Yêu cầu

Node.js >= 18.x

npm hoặc yarn

Các microservices chạy trên các cổng tương ứng:

Auth Service: 8001

Chat Service: 8002

Admin Service: 8003

Post Service: 8004

1.⚡ Cài đặt

Clone repo:

git clone <repo-url>
cd <project-folder>


2. Cài dependencies:

npm install


3. Cài TypeScript và types (nếu chưa có):

npm install -D typescript ts-node @types/node @types/express @types/cors @types/morgan @types/axios

// Cài thêm 
npm install multer 



4. Khởi tạo file tsconfig.json nếu chưa có:

npx tsc --init


Một cấu hình cơ bản:

{
  "compilerOptions": {
    "target": "ES2020",
    "module": "CommonJS",
    "outDir": "./dist",
    "rootDir": "./src",
    "strict": true,
    "esModuleInterop": true
  }
}


5. Tạo file .env ở root:

PORT=4000
FRONTEND_ORIGIN=http://localhost:5173

AUTH_SERVICE_URL=http://127.0.0.1:8001
CHAT_SERVICE_URL=http://127.0.0.1:8002
ADMIN_SERVICE_URL=http://127.0.0.1:8003
POST_SERVICE_URL=http://127.0.0.1:8004


6. Chạy project (development):

npm run dev
# hoặc nếu không có script "dev"
npx ts-node src/server.ts


API Gateway sẽ chạy tại:

http://localhost:4000


Kiểm tra gateway OK:

GET /
Response: "API Gateway OK"
