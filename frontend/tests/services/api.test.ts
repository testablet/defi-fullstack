import { describe, it, expect, vi } from 'vitest';
import { routeApi, statsApi } from '@/services/api';
import axios from 'axios';

vi.mock('axios');
const mockedAxios = axios as any;

describe('API Services', () => {
  describe('routeApi', () => {
    it('should create a route', async () => {
      const mockResponse = {
        data: {
          id: '123',
          fromStationId: 'MX',
          toStationId: 'ZW',
          analyticCode: 'ANA-123',
          distanceKm: 10.5,
          path: ['MX', 'CGE', 'ZW'],
          createdAt: '2025-01-01T00:00:00Z',
        },
      };

      mockedAxios.create.mockReturnValue({
        post: vi.fn().mockResolvedValue(mockResponse),
        interceptors: {
          request: { use: vi.fn() },
        },
      });

      const result = await routeApi.createRoute({
        fromStationId: 'MX',
        toStationId: 'ZW',
        analyticCode: 'ANA-123',
      });

      expect(result).toEqual(mockResponse.data);
    });
  });

  describe('statsApi', () => {
    it('should fetch statistics', async () => {
      const mockResponse = {
        data: {
          from: '2025-01-01',
          to: '2025-01-31',
          groupBy: 'none',
          items: [
            {
              analyticCode: 'ANA-123',
              totalDistanceKm: 100.5,
            },
          ],
        },
      };

      mockedAxios.create.mockReturnValue({
        get: vi.fn().mockResolvedValue(mockResponse),
        interceptors: {
          request: { use: vi.fn() },
        },
      });

      const result = await statsApi.getDistances('2025-01-01', '2025-01-31', 'none');

      expect(result).toEqual(mockResponse.data);
    });
  });
});



