from rest_framework import serializers
from django.contrib.auth.password_validation import validate_password
from .models import User, StaffMember, FoodItem

class UserSerializer(serializers.ModelSerializer):
    password = serializers.CharField(write_only=True, required=True, validators=[validate_password])

    class Meta:
        model = User
        fields = ['id', 'username', 'email', 'password', 'role', 'phone_number', 'hire_date']
        extra_kwargs = {
            'password': {'write_only': True}
        }

    def create(self, validated_data):
        user = User.objects.create_user(**validated_data)
        return user

class UserLoginSerializer(serializers.Serializer):
    username = serializers.CharField(required=True)
    password = serializers.CharField(required=True)

class StaffMemberSerializer(serializers.ModelSerializer):
    user = UserSerializer()

    class Meta:
        model = StaffMember
        fields = ['user', 'salary', 'employment_status']

    def create(self, validated_data):
        user_data = validated_data.pop('user')
        user = User.objects.create_user(**user_data)
        staff_member = StaffMember.objects.create(user=user, **validated_data)
        return staff_member

class FoodItemSerializer(serializers.ModelSerializer):
    class Meta:
        model = FoodItem
        fields = ['id', 'name', 'price', 'description']
