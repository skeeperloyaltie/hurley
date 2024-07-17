from django.urls import path, include
from rest_framework.routers import DefaultRouter
from .views import UserViewSet, StaffMemberViewSet, FoodItemViewSet

router = DefaultRouter()
router.register(r'users', UserViewSet)
router.register(r'staff', StaffMemberViewSet)
router.register(r'food', FoodItemViewSet)

urlpatterns = [
    path('', include(router.urls)),
]
