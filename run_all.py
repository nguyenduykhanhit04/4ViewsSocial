import subprocess
import time
from pathlib import Path

BASE_DIR = Path(__file__).resolve().parent

# === CONFIG — CHO PHÉP NHIỀU LỆNH MỖI SERVICE ===
SERVICES = {
    "Laravel Backend": {
        "cwd": BASE_DIR / "auth-service",
        "commands": [
            # "composer install",
            "php artisan serve --port=8001"
        ]
    },
    "Laravel Backend Admin": {
        "cwd": BASE_DIR / "admin-service",
        "commands": [
            "php artisan serve --port=8003"
        ]
    },
    "Laravel Backend Time Line": {
        "cwd": BASE_DIR / "timeline-service",
        "commands": [
            # "composer install",
            "php artisan serve --port=8004"
        ]
    },
    "API Gateway": {
        "cwd": BASE_DIR / "api-gateway",
        "commands": [
            # "npm install",
            "npm run dev"
        ]
    },
    "Frontend": {
        "cwd": BASE_DIR / "4viewsocial_client",
        "commands": [
            # "npm install",
            "npm run dev"
        ]
    }, 
    "API Chat": {
        "cwd": BASE_DIR / "chat-service",
        "commands": [
            # "composer install",
            "npm run dev"
        ]
    },
}

def run_service(title, cwd, commands):
    print(f"[START] {title}: {cwd}")

    # Gộp nhiều lệnh thành 1 chuỗi: cmd1 && cmd2 && cmd3
    cmd_chain = " && ".join(commands)

    # mở cửa sổ cmd mới chạy các lệnh nối với nhau
    subprocess.Popen(
        f'start "{title}" cmd /K "cd /D {cwd} && {cmd_chain}"',
        shell=True
    )

def main():
    print("=== STARTING SERVICES ===")

    for title, service in SERVICES.items():
        cwd = service["cwd"]
        commands = service["commands"]

        if cwd.exists():
            run_service(title, cwd, commands)
            time.sleep(1)  # delay nhẹ giữa các service
        else:
            print(f"[WARN] Folder không tồn tại: {cwd}")

    print("\n→ DONE! Check các cửa sổ CMD.")


if __name__ == "__main__":
    main()
