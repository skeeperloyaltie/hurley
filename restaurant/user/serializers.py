from rest_framework import serializers
from django.contrib.auth.password_validation import validate_password
from .models import User, StaffMember, FoodItem

from rest_framework import serializers
from django.contrib.auth.password_validation import validate_password
from .models import User, StaffMember

class UserSerializer(serializers.ModelSerializer):
    password = serializers.CharField(write_only=True, required=True, validators=[validate_password])
    salary = serializers.DecimalField(max_digits=10, decimal_places=2, required=False)
    employment_status = serializers.CharField(max_length=20, required=False)

    class Meta:
        model = User
        fields = ['id', 'username', 'email', 'password', 'role', 'phone_number', 'hire_date', 'salary', 'employment_status']
        extra_kwargs = {
            'password': {'write_only': True}
        }

    def create(self, validated_data):
        salary = validated_data.pop('salary', None)
        employment_status = validated_data.pop('employment_status', None)
        
        user = User.objects.create_user(
            username=validated_data['username'],
            email=validated_data['email'],
            password=validated_data['password'],
            role=validated_data['role'],
            phone_number=validated_data['phone_number']
        )
        
        if salary and employment_status:
            StaffMember.objects.create(user=user, salary=salary, employment_status=employment_status)
        
        return user

    def to_representation(self, instance):
        rep = super().to_representation(instance)
        if hasattr(instance, 'staffmember'):
            rep['salary'] = instance.staffmember.salary
            rep['employment_status'] = instance.staffmember.employment_status
    return rep

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
        password = user_data.pop('password')
        user = User.objects.create_user(password=password, **user_data)
        staff_member = StaffMember.objects.create(user=user, **validated_data)
        return staff_member

class FoodItemSerializer(serializers.ModelSerializer):
    class Meta:
        model = FoodItem
        fields = ['id', 'name', 'price', 'description']
