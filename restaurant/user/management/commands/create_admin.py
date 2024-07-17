# restaurant/user/management/commands/create_admin.py
from django.core.management.base import BaseCommand
from user.models import User

class Command(BaseCommand):
    help = 'Creates a superuser account'

    def handle(self, *args, **options):
        if not User.objects.filter(username="admin").exists():
            User.objects.create_superuser("admin", "admin@restaurant.com", "adminpassword")
            self.stdout.write(self.style.SUCCESS('Admin user has been created'))
        else:
            self.stdout.write(self.style.SUCCESS('Admin user already exists'))